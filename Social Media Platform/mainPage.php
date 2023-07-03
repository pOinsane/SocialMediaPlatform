<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Button Form Example</title>
    <link rel="stylesheet"  href="style.css">
</head>
<body class="main-page">
<div class="background-image"></div>

<header class="logo">
        <img src="images/logo.png" alt="Logo">
    </header>
    <div class="content">

    <h1>Welcome To CTISocial</h1>
    <h2>Please login if you have an account, if not you are welcome to register</h2>
    
    <form method="post">
        <button type="submit" name="login">Login</button>
        <button type="submit" name="register">Register</button>
    </form>

    </div>
    <?php
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        if (isset($_POST["login"])) {
            header("Location: ./login.php");
            exit;
        } elseif (isset($_POST["register"])) {
            header("Location: ./registration.php");
            exit;
        }

    }
        
    ?>
    
</body>
</html>