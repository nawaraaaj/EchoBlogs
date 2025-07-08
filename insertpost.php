<?php

session_start();
$user_id = $_SESSION['id'] ?? null;

include 'db.php';

$categoryOptions = '';

if (!$user_id) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['role'] == "author") {
    // Fetch categories for dropdown
    $sql = "SELECT * FROM categories;";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "Error: " . $conn->error;
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryOptions .= "<option value='{$row['name']}'>{$row['name']}</option>";
        }

        // Form submission
        if (isset($_POST['submit'])) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $category_name = $_POST['category_name'];

            // Image upload
            $name = $_FILES['image']['name'];
            $temp_location = $_FILES['image']['tmp_name'];
            $our_location = "uploads/";

            if (!empty($name)) {
                move_uploaded_file($temp_location, $our_location . $name);
            }

            // Get category_id based on category name
            $sql1 = "SELECT id FROM categories WHERE name = '$category_name';";
            $result1 = mysqli_query($conn, $sql1);

            if ($result1 && $result1->num_rows > 0) {
                $row = mysqli_fetch_assoc($result1);
                $category_id = $row['id'];

                // Insert into posts
                $sql2 = "INSERT INTO posts (title, content, author_id, category_id, image) 
                         VALUES ('$title', '$content', '$user_id', '$category_id', '$name');";

                $result2 = mysqli_query($conn, $sql2);

                if ($result2) {
                    $post_id = mysqli_insert_id($conn);
                    echo "<script>alert('Post added successfully!');</script>";
                    header("Location: postview.php?id=" . $post_id);
                } else {
                    echo "Error inserting post: " . $conn->error;
                }

            } else {
                echo "Category not found.";
            }
        }
    }

} else {
    header("Location: dashboard.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Post</title>
    <link rel="stylesheet" href="style.css">
    
</head>

<body>
    <form action="insertpost.php" method="POST" enctype="multipart/form-data" class="insertbox">
        <h2 style="text-align: center;">Insert New Post</h2>

        <label for="title">Title:</label>
        <input type="text" name="title" required>

        <label for="content">Content:</label>
        <textarea name="content" rows="5" placeholder="Write your content here..." required></textarea>


        <label for="category">Select Category:</label>
        <select name="category_name" id="category" required>
            <option value="" disabled selected>Choose a category</option>
            <?php echo $categoryOptions; ?>
        </select>

        <label for="image">Image:</label>
        <input type="file" name="image" accept="uploads/*">

        <input type="submit" name="submit" value="Add Post">
    </form>
</body>

</html>