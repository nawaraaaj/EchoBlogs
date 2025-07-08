<?php
session_start();
include "db.php";

$post_id = (int)$_GET['post_id'];


if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    echo "You must be logged in to comment.";
    exit;
}

if (isset($_POST['submit_comment'])) {
    $user_name = $_SESSION['name'];
    $email = $_SESSION['email'];
    $comment = trim($_POST['comment']);
    $post_id = (int) $_POST['post_id'];


    if (empty($comment)) {
        echo "Comment cannot be empty.";
        exit;
    }

    $sql = "INSERT INTO comments (post_id, user_name, email, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $post_id, $user_name, $email, $comment);

    if ($stmt->execute()) {
        header("Location:viewpost.php?post_id=" . $post_id);
        exit;
    } else {
        echo "Error submitting comment.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>