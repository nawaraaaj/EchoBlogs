<?php
session_start();
include "db.php"; // Assuming db.php handles your database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <link rel="stylesheet" href="css/displaypost.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="header-content">
            <h1 class="logo">EchoBlogs</h1>
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    </header>

    <main class="main-container">
        <div class="post-grid">
            <?php
            $sql = "SELECT id, title, content, image FROM posts ORDER BY id DESC;";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Truncate content for preview
                    $content_preview = strlen($row['content']) > 100 ? substr($row['content'], 0, 100) . "..." : $row['content'];
            ?>
                    <div class="post-card">
                        <?php if (!empty($row['image'])) { ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="post-image" onerror="this.style.display='none'">
                        <?php } ?>
                        <div class="post-content">
                            <h3 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="post-excerpt"><?php echo htmlspecialchars($content_preview); ?></p>
                            <div class="post-actions">
                                <a href="viewpost.php?post_id=<?php echo $row['id']; ?>" class="btn btn-view">View</a>
                                <a href="updatepost.php?post_id=<?php echo $row['id']; ?>" class="btn btn-update">Update</a>
                                <a href="deletepost.php?post_id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='no-posts'>No posts found. Why not create one?</p>";
            }
            ?>
        </div>
    </main>

</body>
</html>
