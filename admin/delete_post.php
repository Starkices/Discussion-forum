<?php
// delete_post.php

require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        header("Location: my_posts.php?msg=Post+deleted+successfully");
        exit();
    } else {
        echo "Error deleting post.";
    }

    $stmt->close();
} else {
    echo "No post ID specified.";
}
?>