<?php
// Connect to the database using PDO
$db = new PDO("mysql:host=localhost;dbname=projectNew;charset=utf8mb4", "root", "root");

// Retrieve the post_id from the request data
$postId = $_GET['post_id'];

// Prepare the SQL statement with a parameter for the post_id
$stmt = $db->prepare("SELECT COUNT(*) AS dislikeCount FROM dislikes WHERE post_id = :postId");
$stmt->bindParam(':postId', $postId);
$stmt->execute();

// Fetch the result
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$dislikeCount = $result['dislikeCount'];

// Return the dislike count as JSON response
echo json_encode($dislikeCount);
?>
