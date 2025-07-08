<?php

session_start();
// get username from session
if (isset($_SESSION['name'])) {
    $user_name = $_SESSION['name'];
}

include "db.php";

if (!isset($_GET['post_id'])) {
    die("Post ID is missing.");
}

$post_id = (int) $_GET['post_id'];

$sql = "SELECT posts.*, categories.name AS category_name FROM posts 
        JOIN categories ON posts.category_id = categories.id 
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found.");
}

$post = $result->fetch_assoc();

// Fetch comments for the post
$sql2 = "SELECT user_name, comment FROM comments WHERE post_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql2);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result2 = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>
    <title>View Post</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($post['category_name']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

    <?php if ($post['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" width="300">
    <?php endif; ?>


    <!-- Comments Section -->

    <form action="insertcomment.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['post_id']); ?>">
        <textarea name="comment" id="comments" placeholder="Write your comment..." required></textarea><br>
        <button type="submit" name="submit_comment">Post Comment</button>
    </form>

    <h3>Comments</h3>

<?php if ($result2->num_rows > 0): ?>
    <?php while ($row2 = $result2->fetch_assoc()): ?>
        <div class="comment-box">
            <p><strong><?php echo htmlspecialchars($row2['user_name']); ?></strong></p>
            <p><?php echo nl2br(htmlspecialchars($row2['comment'])); ?></p>
            <hr>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No comments yet. Be the first to comment!</p>
<?php endif; ?>


</body>

</html>