<?php
session_start();
require "userDb.php";

// Retrieve user data from the AJAX request
$userData = $_POST['userData'];

// Retrieve the post ID from the GET request
$postId = $_GET['post_id'];

// Prepare the SQL statement to retrieve the comments for the post
$sql = "SELECT c.content, u.name AS username
        FROM comments AS c
        JOIN users AS u ON c.user_id = u.id
        WHERE c.post_id = :postId
        ORDER BY c.timestamp DESC";

$stmt = $db->prepare($sql);
$stmt->bindParam(':postId', $postId);
$stmt->execute();

// Fetch all the comments
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Send the comments data as JSON response
header('Content-Type: application/json');
echo json_encode($comments);
?>