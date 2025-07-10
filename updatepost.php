<?php
session_start();
include 'db.php';

// --- Authorization and Initial Setup ---

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'author') {
    header("Location: dashboard.php");
    exit();
}
$user_id = $_SESSION['id'];


if (!isset($_GET['post_id'])) {
    die("Error: Post ID is missing.");
}
$post_id = (int)$_GET['post_id'];
$error_message = '';


$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Error: Post not found.");
}
$post = $result->fetch_assoc();
$stmt->close();


$categoryOptions = '';
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
if ($categories_result) {
    while ($cat = mysqli_fetch_assoc($categories_result)) {
        $isSelected = ($cat['id'] == $post['category_id']) ? 'selected' : '';
        $categoryOptions .= "<option value='{$cat['id']}' {$isSelected}>" . htmlspecialchars($cat['name']) . "</option>";
    }
}



if (isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $image_name = $post['image']; 

    if (empty($title) || empty($content) || empty($category_id)) {
        $error_message = "Please fill in all required fields.";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed_types)) {
                $new_image_name = time() . '_' . basename($_FILES['image']['name']);
                $target_path = "uploads/" . $new_image_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    if (!empty($image_name) && file_exists("uploads/" . $image_name)) {
                        unlink("uploads/" . $image_name);
                    }
                    $image_name = $new_image_name; 
                } else {
                    $error_message = "Failed to upload new image.";
                }
            } else {
                $error_message = "Invalid file type. Please upload a JPG, PNG, or GIF.";
            }
        }

        if (empty($error_message)) {
            $update_stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?");
            $update_stmt->bind_param("ssisi", $title, $content, $category_id, $image_name, $post_id);

            if ($update_stmt->execute()) {
                header("Location: viewpost.php?post_id=" . $post_id . "&status=updated");
                exit();
            } else {
                $error_message = "Error: Could not update the post. " . $update_stmt->error;
            }
            $update_stmt->close();
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
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/updatepost.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="header-content">
            <h1 class="logo">Edit Post</h1>
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    </header>

    <main class="main-container">
        <div class="form-container">
            <form action="updatepost.php?post_id=<?php echo $post_id; ?>" method="POST" enctype="multipart/form-data">
                <h2>Refine Your Post</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category_id" required>
                        <?php echo $categoryOptions; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Featured Image</label>
                    <?php if (!empty($post['image'])): ?>
                        <div class="current-image">
                            <p>Current Image:</p>
                            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Current featured image" class="current-image-preview">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
                    <p class="field-hint">Upload a new image to replace the current one.</p>
                </div>

                <div class="form-group">
                    <button type="submit" name="submit">Update Post</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
