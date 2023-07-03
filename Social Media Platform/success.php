<h1 class="fade-in">You registered succesfully!</h1>
<h3 class="fade-in">You may login to your account now by using the button below</h3>

<form method="post">
        <button type="submit" name="login">Login</button>
</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST["login"])) {
            // Redirect to page1.php
            header("Location: ./login.php");
            exit;
        }
}

?>

<style>
        body {
        font-family: Arial, sans-serif;
        margin-top: 20%;
        background-image: url(images/giphy.gif);
        text-align: center;
        color: aliceblue;
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


    @keyframes fade {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  .fade-in {
    animation: fade 1.5s ease-in;
  }

</style>