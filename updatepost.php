<?php

session_start();
$user_id = $_SESSION['id'];
include "db.php";

if (!isset($_GET['post_id'])) {
    die("Post ID is missing");
}

$post_id = (int)$_GET['post_id'];

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
} else {
    if ($_SESSION['role'] == 'author') {


        //fetching the data from database 
        $sql = "SELECT * FROM posts WHERE id = '$post_id';";
        $result = mysqli_query($conn, $sql);
        $postdata = mysqli_fetch_assoc($result);

        if (!$postdata) {
            die("Post not found.");
        }

        $current_category_id = $postdata['category_id'];

        $categories_sql = "SELECT * FROM categories;";
        $categories_result = mysqli_query($conn, $categories_sql);


        //now updating the posts
        if (isset($_POST['submit'])) {
            $id = (int) $_POST['id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $category_id = (int) $_POST['category_name'];
            $existing_image = $_POST['existing_image'];

            if (empty($title) || empty($content) || empty($category_id)) {
                die('Please fill all required fields.');
            }

            var_dump($_POST['category_name']);


            $image = $existing_image;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                $tmpName = $_FILES['image']['tmp_name'];
                $fileName = basename($_FILES['image']['name']);
                $targetFilePath = $uploadDir . $fileName;

                // Validatting file type only allows png,jpeg,gif and webp
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($tmpName);

                if (!in_array($fileType, $allowedTypes)) {
                    die('Only JPG, PNG, GIF, and WEBP images are allowed.');
                }

                // Generating a unique filename to prevent overwriting
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $uniqueFileName = uniqid('img_', true) . '.' . $fileExt;
                $targetFilePath = $uploadDir . $uniqueFileName;

                if (!move_uploaded_file($tmpName, $targetFilePath)) {
                    die('Error uploading the file.');
                }

                // Delete old image file if it is different
                if ($existing_image && file_exists($uploadDir . $existing_image) && $existing_image !== $uniqueFileName) {
                    unlink($uploadDir . $existing_image);
                }
                $image = $uniqueFileName;
            }

            
            $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, category_id=?, image=? WHERE id=?");
            $stmt->bind_param("ssisi", $title, $content, $category_id, $image, $id);


            if ($stmt->execute()) {
                echo "Post updated successfully.";
                header("Location: viewpost.php?post_id=" . $post_id);
                exit();
            } else {
                echo "Error updating post: " . $stmt->error;
            }

            $stmt->close();
        }

    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Update Post</title>
</head>

<body>

    <form action="" method="POST" enctype="multipart/form-data" class="updatebox">
        <h2 style="text-align: center;">Update Post</h2>

        <!-- Hidden field to keep track of post ID-->
        <input type="hidden" name="id" value="<?php echo $postdata['id']; ?>">


        <label for="title">Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($postdata['title']) ?>" required>

        <label for="content">Content:</label>
        <textarea name="content" rows="5" placeholder="Write your content here..."
            required><?php echo htmlspecialchars($postdata['content']) ?> </textarea>


        <label for="category">Select Category:</label>
        <select name="category_name" id="category" required>
            <option value="" disabled>Choose a category</option>
            <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $postdata['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="image">Image:</label>
        <input type="file" name="image" accept="image/*">
        <!-- Storing the existing file name in a hidden input -->
        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($postdata['image']); ?>">
        <!-- Showing the existing filename -->
        <p>Current uploaded file: <?php echo htmlspecialchars($postdata['image']); ?></p>

        <input type="submit" name="submit" value="Update Post">
    </form>

</body>

</html>