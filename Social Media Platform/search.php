


<?php
require "userDb.php";
if(isset($_POST["sbmtBtn"])){
    extract($_POST);
 
    $st = $db->prepare("SELECT user_id, friend_id FROM friends WHERE user_id = ? AND friend_id = ?");
    $st->execute([$sender, $receiver]);
    $st2 = $db->prepare("SELECT user_id, friend_id FROM friends WHERE friend_id = ? AND user_id = ?");
    $st2->execute([$sender, $receiver]);

    if ($st->rowCount() > 0 || $st2->rowCount() > 0){

        echo "<div><h2 id='bounce-heading'>You are already friends!</h2>";
        echo "<button><a style='text-decoration:none; color:inherit;' href='userPage.php'> Go Back to the Main Page</a></button></div>";
    }else {
    $type="Friend Request";
    $content="Would you like to be friends?";

    $stmt=$db->prepare("insert into notifications (from_id,to_user_id,type,content) values (?, ?, ?, ?)");
    $stmt->execute([$sender,$receiver,$type,$content]);
    

    echo "<div><h2>You successfully sended a friendship request to the user with the id ". $receiver."</h2>";
    echo "<button><a style='text-decoration:none; color:inherit;' href='userPage.php'> Go Back to the Main Page</a></button></div>";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Sended</title>
  <style>
    div{
      margin: 20% auto ;
      width: 300px;
      padding: 20px;
      vertical-align: middle;
    }
    body {
      background-color: #f2f2f2;
      background-image: url(images/giphy.gif);
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 0;
      padding: 0;
    }

    .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    h2 {
      color: white;
      font-size: 24px;
      margin-bottom: 20px;
      position: relative;
      animation: bounce 1s infinite;
    }

    button {
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
    }

    button:hover {
      background-color: #45a049;
    }

    @keyframes bounce {
      0%, 100% {
        top: 0;
      }
      50% {
        top: -20px;
      }}
  </style>
<script>
    // JavaScript code to trigger the animation
    const heading = document.getElementById('bounce-heading');
    heading.addEventListener('mouseover', startBounce);
    heading.addEventListener('mouseout', stopBounce);

    function startBounce() {
      heading.style.animationPlayState = 'running';
    }

    function stopBounce() {
      heading.style.animationPlayState = 'paused';
    }
  </script>

</head>
</head>
<body>
    
</body>
</html>