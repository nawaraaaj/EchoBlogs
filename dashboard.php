
<?php
session_start();

include "db.php";

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}
else{

    $sql = "
    SELECT
        (SELECT COUNT(*) FROM posts) AS total_posts,
        (SELECT COUNT(*) FROM categories) AS total_categories,
        (SELECT COUNT(*) FROM comments) AS total_comments,
        (SELECT COUNT(*) FROM views) AS total_views
    ;";

    $result = mysqli_query($conn,$sql);
    if($result){
        $row = mysqli_fetch_assoc($result);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EchoBlogs</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>EchoBlog</h1>
            </div>
            <div class="user-info">
                <span class="welcome-text">Welcome, <?php echo $_SESSION['name']; ?></span>
                <span class="role-badge"><?php echo $_SESSION['role']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="main-container">
    
        <div class="welcome-section">
            <h2>Welcome to Your Dashboard</h2>
            <p>Manage your content, categories, and posts from here.</p>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-grid">
            <!-- Categories Card -->
            <div class="dashboard-card">
                <div class="card-icon">📁</div>
                <h3>Categories</h3>
                <p>Create and manage content categories for better organization.</p>
                <a href="addcategory.php" class="card-button">Add Category</a>
            </div>

            <!-- Posts Management Card -->
            <div class="dashboard-card">
                <div class="card-icon">📝</div>
                <h3>Create Post</h3>
                <p>Write and publish new content to your website.</p>
                <a href="insertpost.php" class="card-button">Create Post</a>
            </div>

            <!-- Display Posts Card -->
            <div class="dashboard-card">
                <div class="card-icon">👁️</div>
                <h3>View Posts</h3>
                <p>Browse and manage all your published content.</p>
                <a href="displaypost.php" class="card-button">Display Posts</a>
            </div>
        </div>

        <div class="stats-section">
            <h2>Quick Stats</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                         <?php echo $row['total_posts']; ?>
                    </div>
                    <div class="stat-label">Total Posts</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php echo $row['total_categories']; ?>
                    </div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $row['total_views']; ?></div>
                    <div class="stat-label">Views</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $row['total_comments']; ?></div>
                    <div class="stat-label">Comments</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="activity-section">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon">📝</div>
                    <div class="activity-content">
                        <p>No recent activity to display</p>
                        <span class="activity-time">Start by creating your first post!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 EchoBlogs. All rights reserved.</p>
    </footer>
</body>

</html>
