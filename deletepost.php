<?php

session_start();
include "db.php";

if (!isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = $_GET['post_id'];
$current_user_id = $_SESSION['id'];
$current_user_role = $_SESSION['role'];

$sql = "SELECT author_id FROM posts where id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows == 0) {
    echo "Post not found";
    exit;
}

$post = $result->fetch_assoc();
$stmt->close();

if ($current_user_role === 'admin' || $post['author_id'] == $current_user_id) {
    $delete_sql = "DELETE FROM posts where id=?;";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $post_id);
    if ($delete_stmt->execute()) {
        $_SESSION['message'] = "Post deleted successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error deleting post.";
    }
    $delete_stmt->close();
} else {
    echo "You don't have permission to delete this post.";
}
?>