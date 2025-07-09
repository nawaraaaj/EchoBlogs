<?php
session_start();
include "db.php";

// Check for post_id
if (!isset($_GET['post_id'])) {
    die("Error: Post ID is missing.");
}

$post_id = (int)$_GET['post_id'];

// Prepare and execute statement to get the post details
$sql = "SELECT p.title, p.content, p.image, c.name AS category_name 
        FROM posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Post not found.");
}
$post = $result->fetch_assoc();

// Fetch comments for the post
$sql2 = "SELECT user_name, comment FROM comments WHERE post_id = ? ORDER BY id DESC";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $post_id);
$stmt2->execute();
$comments_result = $stmt2->get_result();

// view post 
$post_id = $_GET['post_id'];
$sql3 = "INSERT INTO views (post_id) VALUES (?)";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("i", $post_id);
$stmt3->execute();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/viewpost.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="header-content">
            <a href="displaypost.php" class="back-link">&larr; All Posts</a>
            <a href="dashboard.php" class="dashboard-link">Dashboard</a>
        </div>
    </header>

    <main class="main-container">
        <div class="post-container">
            <p class="post-category"><?php echo htmlspecialchars($post['category_name']); ?></p>
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <?php if (!empty($post['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
            <?php endif; ?>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>

        <div class="comments-section">
            <h2>Join the Discussion</h2>
            <form action="insertcomment.php" method="POST" class="comment-form">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <textarea name="comment" placeholder="Write your comment here..." required></textarea>
                <button type="submit" name="submit_comment">Post Comment</button>
            </form>

            <h3 class="comments-title">Comments (<?php echo $comments_result->num_rows; ?>)</h3>
            <div class="comments-list">
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment-box">
                            <p class="comment-author"><strong><?php echo htmlspecialchars($comment['user_name']); ?></strong></p>
                            <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No comments yet. Be the first to share your thoughts!</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

</body>
</html>
