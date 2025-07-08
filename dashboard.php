
<?php

session_start();

if(!isset($_SESSION['id'])){
    header("Location: index.php");
}else{
    echo"Welcome to dashboard ". $_SESSION['name']."<br>";
    echo "Your role is ". $_SESSION['role']."<br>";
}

?>

<a href="logout.php">Logout</a><br>
<a href="addcategory.php">Add category</a><br>
<a href="insertpost.php">Insert Post</a><br>
<a href="updatepost.php">Update Post</a><br>
<a href="displaypost.php">Display Post</a><br>
<link rel="stylesheet" href="style.css">