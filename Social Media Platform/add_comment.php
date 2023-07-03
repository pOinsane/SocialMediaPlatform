<?php
session_start();
require "userDb.php";

// Retrieve the POST data
$postId = $_POST['postId'];
$commentContent = $_POST['comment'];
$userData = $_POST['userData'];
$userId = $userData['id'];

// Prepare the SQL statement to insert the comment
$sql = "INSERT INTO comments (post_id, user_id, content, timestamp)
        VALUES (:postId, :userId, :content, NOW())";

$stmt = $db->prepare($sql);
$stmt->bindParam(':postId', $postId);
$stmt->bindParam(':userId', $userId);
$stmt->bindParam(':content', $commentContent);


// Execute the statement
if ($stmt->execute()) {
    // Comment added successfully
    $response = array('success' => true);
} else {
    // Failed to add comment
    $response = array('success' => false);
}

// Send the JSON response back
header('Content-Type: application/json');
echo json_encode($response);

?>