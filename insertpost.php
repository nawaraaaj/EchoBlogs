<?php
session_start();
include 'db.php';

// Check if user is logged in and is an author
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'author') {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['id'];
$error_message = '';
$success_message = '';

// Fetch categories for the dropdown
$categoryOptions = '';
$category_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$category_result = mysqli_query($conn, $category_sql);
if ($category_result) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $categoryOptions .= "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
    }
}

// Handle form submission
if (isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $image_name = '';

    // Basic validation
    if (empty($title) || empty($content) || empty($category_id)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed_types)) {
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $target_path = "uploads/" . $image_name;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $error_message = "Failed to upload image.";
                    $image_name = ''; // Reset image name on failure
                }
            } else {
                $error_message = "Invalid file type. Please upload a JPG, PNG, or GIF.";
            }
        }

        // Proceed if no errors so far
        if (empty($error_message)) {
            // Insert post into the database using prepared statements
            $stmt = $conn->prepare("INSERT INTO posts (title, content, image, author_id, category_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssii", $title, $content, $image_name, $user_id, $category_id);

            if ($stmt->execute()) {
                $post_id = $stmt->insert_id;
                header("Location: viewpost.php?post_id=" . $post_id . "&status=success");
                exit();
            } else {
                $error_message = "Error: Could not save the post. " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="css/insertpost.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="header-content">
            <h1 class="logo">Create a Post</h1>
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    </header>

    <main class="main-container">
        <div class="form-container">
            <form action="insertpost.php" method="POST" enctype="multipart/form-data">
                <h2>Compose Your New Masterpiece</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter a catchy title" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="10" placeholder="Write your amazing content here..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category_id" required>
                        <option value="" disabled selected>Select a category</option>
                        <?php echo $categoryOptions; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Featured Image (Optional)</label>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
                </div>

                <div class="form-group">
                    <button type="submit" name="submit">Publish Post</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
