<?php
session_start();
require "userDb.php";

$userData = $_SESSION["user"];

// check if the user authenticated before
if (!validSession()) {
    header("Location: login.php?error"); // redirect to login page
    exit;
}

if (isset($_POST["accept"])) {

    extract($_POST);

    $stmt = $db->prepare("insert into friends (user_id,friend_id) values (? , ?)") ;
    $stmt->execute([$from_id,$to_id]) ;


    $st = $db->prepare("delete from notifications where id = (?)") ;
    $st->execute([$notId]) ;
    
    // echo "<p>istek gönderildi</p>";
    exit;
}


function csrf_check() { 
    if ( isset($_POST["csrf_token"]) && isset($_COOKIE["secret"])) {
        return password_verify($_COOKIE["secret"] . SALTING  , $_POST["csrf_token"]) ;
    }
    return false; 
 }

 function create_csrf_token() {
    $secret = bin2hex(random_bytes(10)) ; // create random 10 bytes (20 hex digits)
    setcookie("secret", $secret) ; // session cookie
    return password_hash($secret . SALTING, PASSWORD_BCRYPT) ;
 }

if(isset($_POST["reject"])){
    extract($_POST);

    $st = $db->prepare("delete from notifications where id = (?)") ;
    $st->execute([$not_id]) ;

    exit;
}

if(isset($_POST["delete"])){
    extract($_POST);

    $type="Remove Friend";
    $content=$userData["name"] ." " .$userData["surname"] ." removed you from friendship";

    $stmt = $db->prepare("insert into notifications (from_id,to_user_id,type,content) values (? , ?, ?, ?)") ;
    $stmt->execute([$toWhom,$who,$type,$content]) ;

    $st = $db->prepare("delete from friends where user_id = (?) and friend_id= (?)") ;
    $st->execute([$who,$toWhom]) ;
    
    exit;
}

//FOR POST FUNCTION TO WORK PROPERLY//
// Check if a post was just added
$postAdded = false;
if (isset($_SESSION["postAdded"]) && $_SESSION["postAdded"]) {
    $postAdded = true;
    unset($_SESSION["postAdded"]);
}

// Handle the form submission
if (isset($_POST["btnAddPost"])) {
    extract($_POST);

    require_once 'Upload.php';

    // Retrieve data
    $user_id = $userData["id"];
    $timestamp = date("Y-m-d H:i:s");

    // Upload profile picture
    $post = new Upload("content", "posts");

    if ($post->error) {
        echo "Error: " . $post->error;
    } else {
        // Insert user data into the database only if a post was not just added
        if (!$postAdded) {
            $stmt = $db->prepare("INSERT INTO posts (user_id, content, timestamp) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $post->filename, $timestamp]);
        }

        // Set the flag to indicate that a post was added
        $_SESSION["postAdded"] = true;

        // Redirect to the same page to prevent duplicate form submission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
//END POST OPERATION

//PAGE OPR
$postsPerPage = 10; // Number of posts to display per page
$pageNumber = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
?>

<!DOCTYPE html>
<html>

<head>
    <title>Social Network</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="jquery-3.7.0.min.js"></script>
    <script src="action.js"></script>
    <style>
    /* Styling the container */
*{
    font-family: Arial, Helvetica, sans-serif;
}
html{
    background-color: antiquewhite;
}
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f8f8f8;
  border: 1px solid #e6e6e6;
  border-radius: 5px;
  text-align: center; /* Add text-align property to center the content */
}

/* Styling the post div */
.post {
  background-color:blanchedalmond;
  padding: 20px;
  margin-bottom: 20px;
  text-align: left; /* Reset text-align property for the post content */
  border: 1px solid #e6e6e6;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: inline-block; /* Display the posts in a line */
}

.post img {
    height:100px;
    width:100px;
}


/* Styling the headings */
h1 {
  color: #333;
  font-size: 28px;
  margin-bottom: 20px;
}

h2 {
  color: #555;
  font-size: 24px;
  margin-bottom: 10px;
}

/* Styling the list */
ul {
  list-style-type: none;
  padding: 0;
  margin-bottom: 20px;
}

li {
  margin-bottom: 10px;
}

/* Styling the links */
a {
  color: white;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

/* Styling the button */
button {
  padding: 10px 20px;
  background-color: #007bff;
  border: none;
  color: #fff;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: #0056b3;
}

/* Styling the footer */
.footer {
  background-color: #f8f8f8;
  padding: 10px 20px;
  text-align: center;
  border-top: 1px solid #e6e6e6;
  margin-top: 20px;
}
    </style>
    <script type="text/javascript">

        $(function () {
            // Like button click event
            $("#timeline").on("click", ".likeButton", function () {
                var postId = $(this).data("post-id");
                var userData = <?php echo json_encode($userData); ?>;

                // Reference to the like button element
                var $likeButton = $(this);

                // Send an AJAX request to add the like
                $.ajax({
                    url: "add_like.php",
                    method: "POST",
                    data: { postId: postId, userData: userData },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Like added successfully
                            console.log("Like added.");

                            // Update the dislike count in the UI with latest data from the server
                            getLikeCounts();
                        } else {
                            console.log("Failed to add like.");
                            getLikeCounts()
                        }
                    },
                    error: function () {
                        console.log("Error occurred while adding like.");
                    }
                });
            });

            // Dislike button click event
            $("#timeline").on("click", ".dislikeButton", function () {
                var postId = $(this).data("post-id");
                var userData = <?php echo json_encode($userData); ?>;

                // Reference to the dislike button element
                var $dislikeButton = $(this);

                // Send an AJAX request to add the dislike
                $.ajax({
                    url: "add_dislike.php",
                    method: "POST",
                    data: { postId: postId, userData: userData },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Dislike added successfully
                            console.log("Dislike added.");

                            // Update the dislike count in the UI with latest data from the server
                            getDisLikeCounts();
                        } else {
                            console.log("Failed to add dislike.");
                            getDisLikeCounts();
                        }
                    },
                    error: function () {
                        console.log("Error occurred while adding dislike.");
                    }
                });
            });

            //Comment button event
            $("#timeline").on("click", ".submitCommentButton", function () {
                var postId = $(this).data("post-id");
                var commentInput = $(this).siblings(".commentInput").val();
                var userData = <?php echo json_encode($userData); ?>;

                // Reference to the submit button element
                var $submitCommentButton = $(this);

                // Send an AJAX request to add the comment
                $.ajax({
                    url: "add_comment.php",
                    method: "POST",
                    data: { postId: postId, comment: commentInput, userData: userData },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Comment added successfully
                            console.log("Comment added.");

                            // Clear the comment input field
                            $submitCommentButton.siblings(".commentInput").val("");

                            // Refresh the comments for the post
                            //refreshComments(postId);

                            getCommentsForPost();
                        } else {
                            console.log("Failed to add comment.");
                            getCommentsForPost();
                        }
                    },
                    error: function () {
                        console.log("Error occurred while adding comment.");
                    }
                });
            });
        });

        // Function to retrieve like counts for all posts
        function getLikeCounts() {
            $(".post").each(function () {
                var postId = $(this).find(".likeButton").data("post-id");
                var $postElement = $(this); // Store the reference to the post element
                // Send an AJAX request to retrieve the like count for the post
                $.ajax({
                    url: "get_like_count.php",
                    method: "GET",
                    data: { post_id: postId },
                    dataType: "json",
                    success: function (data) {
                        // Update the like count element with the retrieved value
                        $postElement.find(".likeCount").text("Likes: " + data);
                    },
                    error: function () {
                        console.log("Error occurred while retrieving like count.");
                    }
                });
            });
        }

        
// Retrieve initial like counts
getLikeCounts();

        // Function to retrieve like counts for all posts
        function getDisLikeCounts() {
            console.log("get dislike count");
            $(".post").each(function () {
                var postId = $(this).find(".dislikeButton").data("post-id");
                var $postElement = $(this); // Store the reference to the post element
                // Send an AJAX request to retrieve the like count for the post
                $.ajax({
                    url: "get_dislike_count.php",
                    method: "GET",
                    data: { post_id: postId },
                    dataType: "json",
                    success: function (data) {
                        // Update the like count element with the retrieved value
                        $postElement.find(".dislikeCount").text("Dislikes: " + data);
                    },
                    error: function () {
                        console.log("Error occurred while retrieving like count.");
                    }
                });
            });
        }

//retrive initial dislike counts
getDisLikeCounts();

function getCommentsForPost() {
            console.log("get Comments");
            $(".post").each(function () {
                var postId = $(this).find(".submitCommentButton").data("post-id");
                var $postElement = $(this); // Store the reference to the post element
                // Send an AJAX request to retrieve the like count for the post
                $.ajax({
                    url: "get_comments.php",
                    method: "GET",
                    data: { post_id: postId },
                    dataType: "json",
                    success: function (data) {
                        const contentArray = data.map(obj => obj.username + ": " + obj.content);
                        console.log(data);
                        console.log(contentArray);
                        // Update the like count element with the retrieved value
                        $postElement.find(".Comments").text("Comments:" + contentArray);
                    },
                    error: function () {
                        console.log("Error occurred while retrieving comments");
                    }
                });
            });
        }

    </script>

</head>

<body>
    <div style="position: fixed; bottom: 0; left: 0;">
        <button><a id="logout" href="logout.php">Logout</a></button>
    </div>
<!-- 
    <h3>Welcome
        <?=  $userData["name"] ?> (
        <?= $userData["email"] ?>) -->
        <h3 style='text-align:center' >Welcome <?= htmlspecialchars($userData["name"]) ?> <!-- (<?= htmlspecialchars($userData["email"]) ?>) --></h3>

    <script>
        var userData = <?php echo json_encode($userData); ?>;
    </script>


    <div id="profile-box">
        <button id="notification" onclick="toggleNotifications()"><i class="fa-solid fa-bell"></i></button>
        <img src="./images/<?= $userData["pp"] ?>" alt="Image" width="50" height="50" style="border-radius: 50%;">
        <span style="margin-left:10px;">
            <?= $userData["name"] ?>
            <?= $userData["surname"] ?>
        </span>
    </div>

    <div id="friendList"
        style="position: fixed;top: 50;right: 0; border: 1px solid #ccc; margin-top:13px; width:235px; height:100%;">

        <h3>Your Friends</h3>
        <ul>
            
            <?php
            seeFriendList($userData["id"]);
            ?>

        </ul>
    </div>



    <div id="notificationsContainer" style="display: none;">
        <!-- Placeholder content for notifications -->
        <ul id="notificationList">
            <?php
            $userID = $userData["id"];
            $notifications = getNotifications($userID);
            $friendRequestData = array();
            foreach ($notifications as $notification) {

                echo '<li>' .htmlspecialchars( $notification['content']);

            // echo "<div id='enes'>enes</div>";

            if ($notification['type'] == "Friend Request") {
                $query = "SELECT id,from_id, to_user_id FROM notifications WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$notification['id']]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // No notifications yazdır

                $friendName="SELECT name,surname from users where id = ?";
                $st=$db->prepare($friendName);
                $st->execute([$row['from_id']]);
                $row2 = $st->fetch(PDO::FETCH_ASSOC);
                echo "<br>";
               // echo "-". $row2['name']. " ". $row2['surname'];
              
                echo "- " . htmlspecialchars($row2['name']) . " " . htmlspecialchars($row2['surname']);

                echo '<div class="button-container">';
                echo "<div class='invisible'>".$row['id']."</div>";
                echo "<div class='invisible'>".$row['from_id']."</div>";
                echo "<div class='invisible'>".$row['to_user_id']."</div>";
                echo '<button class="accept-btn accept" style="cursor:pointer;"><img src="./images/accept-button.png" alt="Accept" style="width: 20px; height: 20px;"></button>';
                echo '<button class="reject-btn reject" style="cursor:pointer;"><img src="./images/reject-button.png" alt="Reject" style="width: 20px; height: 20px;"></button>';
                echo '</div>';
            }
            echo '</li>';
        }
        ?>
    </ul>
    

    </div>
    </div>

    <br><br><br><br>

    <form class="search-form" action="" method="post" style='text-align:center'>
        <!-- <input type="text" id="searchText" name="friendSearch">
        <input type="submit" value="Search Friend" name="btnFriend"> -->
        <input type="text" id="searchText" name="friendSearch" value="<?= isset($_POST['friendSearch']) ? htmlspecialchars($_POST['friendSearch']) : '' ?>">
         <input type="submit" value="Search Friend" name="btnFriend">
    </form>
    

    <div id="searchPart">
        <br>
        <?php
        if (isset($_POST["btnFriend"])) {
            extract($_POST);
            if (!empty($friendSearch)) {
                $searched = searchFriend($friendSearch, $userData["id"]);
            }
        }
        ?>
        <br>

        <?php
        if (!empty($friendSearch)) {
            foreach ($searched as $s) {
                ?>
                <form action="search.php" method="POST">
                    <input type="hidden" name="sender" value="<?= $userData["id"] ?>">
                    <input type="hidden" name="receiver" value="<?= $s["id"] ?>">
                    <?php echo "<div>";
                    echo "<img style='border-radius:50%; width:30px; height:30px;' src='images/" . $s["pp"] . "'";
                    echo "<span> <div class='invisible'>" . $s["id"] . "</div>" . $s["name"] . " " . $s["surname"] . " (" . $s["email"] . ") <input type='submit' name='sbmtBtn' value='Send a Request' id='sendRequest'></span> ";
                    echo "</div>";
                    echo " </form>";
            }
        }
        ?>
            <p id="error"></p>
    </div>

    <div id="timeline">
        <?php
        $offset = ($pageNumber - 1) * $postsPerPage; // Calculate the offset
        
        //$userId = $userData["id"];
        $userId = $userData["id"];
        $sql = "SELECT p.*, u.name AS username
        FROM posts AS p
        JOIN users AS u ON p.user_id = u.id
        WHERE p.user_id = :userId
        OR p.user_id IN (
          SELECT friend_id FROM friends WHERE user_id = :userId
        ) OR p.user_id IN (
          SELECT user_id FROM friends WHERE friend_id = :userId
        )
        ORDER BY p.id DESC LIMIT :offset, :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $postsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        $i = 0;

        // Check if there are any posts
        if ($stmt->rowCount() > 0) {
            // Output data of each row
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Display post content
                echo '<div class="post" style="width:500px;">';
                echo '<span class="nameUser">   '.$row["username"].'         &nbsp &nbsp &nbsp &nbsp</span>';
                //echo '<img src="./posts/' . $row["content"] . '" alt="Image" width="100" height="100">';
                echo '<img src="./posts/' . htmlspecialchars($row["content"]) . '" alt="Image" width="100" height="100">';
                echo '<br><br><span>Post ID: <span>' . $row["id"] . '</span></span>';
                echo "<br><br>";

                // Display like count
                echo '<span class="likeCount">Likes: <span id="likeCount_' . $row["id"] . '">&nbsp</span></span>&nbsp&nbsp';

                // Display like button
                echo '&nbsp<button class="likeButton" data-post-id="' . $row["id"] . '">Like</button>&nbsp&nbsp&nbsp';

                // Display dislike count
                echo '<span class="dislikeCount">Dislikes: <span id="dislikeCount_' . $row["id"] . '"></span></span>&nbsp&nbsp&nbsp';
                // Display dislike button
                echo '&nbsp&nbsp<button class="dislikeButton" data-post-id="' . $row["id"] . '">Dislike</button><br><br>';

                echo "<br>";

                 //COMMENT INPUT AND BUTTON and OUTPUT
                 echo '<script>getCommentsForPost(' . $row["id"] . ');</script>';
                 echo '<input type="text" class="commentInput" placeholder="Enter a comment">&nbsp&nbsp&nbsp';
                 echo '<button class="submitCommentButton" data-post-id="' . $row["id"] . '">Submit</button>';
                 echo '&nbsp&nbsp&nbsp<br><br><span class="Comments">Comments: <span id="comment_' . $row["id"] . '"></span></span>';
                echo '</div>';

            }

            if ($stmt->rowCount() >= $postsPerPage) {
                // Calculate the total number of posts for the user
                $totalPostsSql = "SELECT COUNT(*) FROM posts WHERE user_id = :userId";
                $totalPostsStmt = $db->prepare($totalPostsSql);
                $totalPostsStmt->bindParam(':userId', $userId);
                $totalPostsStmt->execute();
                $totalPosts = $totalPostsStmt->fetchColumn();

                // Calculate the total number of pages
                $totalPages = ceil($totalPosts / $postsPerPage);

                // Check if there are more pages to display
                if ($pageNumber < $totalPages) {
                    $nextPage = $pageNumber + 1;
                    echo '<br><button><a href="userPage.php?page=' . $nextPage . '">Next </a></button>';
                }
            }
            if ($pageNumber > 1) {
                $beforePage = $pageNumber - 1;
                echo '<br><button><a href="userPage.php?page=' . $beforePage . '"> Before</a></button>';
            }

        } else {
            echo "No posts found.";
        }

        // Call the function to retrieve and update the like counts
        echo '<script>getLikeCounts();</script>';
        echo '<script>getDisLikeCounts();</script>';
        ?>
    </div>

    <br>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="submit" value="Add Post" name="btnAddPost">
        <input type="file" name="content" id="content">
    </form>

    <?php 

        if ( isset($_POST["btnAddPost"])) {
            extract($_POST);

            require_once 'Upload.php';

            // Retrieve data
            $user_id=$userData["id"];
            $timestamp = date("Y-m-d H:i:s");

            // Upload profile picture
            $post = new Upload("content", "posts");

            if ($post->error) {
                echo "Error: " . $post->error;
            } else {
                // Insert user data into the database
                $stmt = $db->prepare("INSERT INTO posts (user_id, content, timestamp) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $post->filename, $timestamp]);
            
                // Redirect to a success page or display a success message
                echo "POST ADDED";
                exit;
            }
        }
        
    ?>
   
   <br><br><br>

    <script>
    $(document).ready(function(){
        $('.accept').on('click',function(){
            let notId = $(this).prev().prev().prev().text();
            let from_id = $(this).prev().prev().text(); 
            let to_id = $(this).prev().text();
            let parent = $(this).parent().parent();
              $.ajax({
                url: 'userPage.php',
                type: 'post',
                data: {
                  'accept': 1,
                  'from_id': from_id,
                  'to_id': to_id,
                  'notId': notId
                },
                success: function(data) {
                  //parent.html(data);
                    parent.remove();
                
                }
              })
 })

 $('.reject').on('click',function(){
            let not_id = $(this).prev().prev().prev().prev().text(); 
            let parent = $(this).parent().parent();
              $.ajax({
                url: 'userPage.php',
                type: 'post',
                data: {
                  'reject': 1,
                  'not_id': not_id
                },
                success: function(data) {
                  //parent.html(data);
                    parent.remove();
                }
              })
 })

 $('.fa-trash').on('click',function(){
            let who = $(this).prev().prev().text();;
            let toWhom = $(this).prev().text(); 
            let parent = $(this).parent();
              $.ajax({
                url: 'userPage.php',
                type: 'post',
                data: {
                  'delete': 1,
                  'who': who,
                  'toWhom': toWhom
                },
                success: function(data) {
                  //parent.html(data);
                    parent.remove();
                }
              })
 })
});
    </script>

</body>

</html>