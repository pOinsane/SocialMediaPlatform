<?php
    session_start();
    require "userDb.php";
    
    // Retrieve user data from the AJAX request
    $userData = $_POST['userData'];
    
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the post ID from the request data
        $postId = $_POST['postId'];
    
        // Perform the necessary database insertion
        $userId = $userData['id'];
        $timestamp = date("Y-m-d H:i:s");
    
        // Check if the user has already liked the post
        $stmt = $db->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$userId, $postId]);
    
        if ($stmt->rowCount() > 0) {
            // User has already liked the post, return an error response
            $response = ['success' => false, 'message' => 'User has already liked the post'];
        } else {
            // Prepare and execute the SQL statement to insert the like data
            $stmt = $db->prepare("INSERT INTO likes (user_id, post_id, timestamp) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $postId, $timestamp]);
    
            // Check if the insertion was successful
            if ($stmt->rowCount() > 0) {
                // Return a success response
                $response = ['success' => true];
            } else {
                // Return an error response
                $response = ['success' => false, 'message' => 'Failed to add like'];
            }
        }
    } else {
        // Return an error response for invalid request method
        $response = ['success' => false, 'message' => 'Invalid request method'];
    }
    
    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
?>
