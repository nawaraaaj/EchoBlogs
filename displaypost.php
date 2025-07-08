<?php

session_start();
include "db.php";

$sql = "SELECT * FROM posts;";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    echo "{$row['title']}<br>";
    echo "{$row['content']}<br>";
    echo "<img src='uploads/{$row['image']}'><br>";
    echo "<a href='updatepost.php?post_id={$row['id']}'>Update</a><br>";
    echo "<a href='viewpost.php?post_id={$row['id']}'>View</a><br>";
     echo "<a href='deletepost.php?post_id={$row['id']}'>Delete</a><br>";
     echo "<hr><hr>";
}
?>

<link rel="stylesheet" href="displaypost.css">