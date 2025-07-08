<?php
session_start();
include "db.php";
mysqli_report(MYSQLI_REPORT_OFF);


if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {

    $_SESSION['access_denied'] = "Admin access required";
    header("Location: index.php");
    exit();
} else {
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];

        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            $message = "<span style='color: green;'>Category " . htmlspecialchars($name) . " added successfully.</span>";
        } else {
            if ($stmt->errno === 1062) { // MySQL error code for duplicate entry
                $message = "<span style='color: red;'>Category '" . htmlspecialchars($name) . "' already exists.</span>";
            } else {
                $message = "<span style='color: red;'>Error: " . $stmt->error . "</span>";
            }
        }

        $stmt->close();
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Add Category</title>
</head>

<body>
    <form action="addcategory.php" method="post">
        <label for="Category">Add new category</label>
        <input type="text" name="name">
        <input type="submit" name="submit" value="Add Category">

        <a href="dashboard.php">Dashboard</a>

        <?php if (!empty($message)): ?>
            <p style="margin-top: 10px;"><?php echo $message; ?></p>
        <?php endif; ?>

    </form>
</body>

</html>