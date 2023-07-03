    <?php
    session_start();
    require "userDb.php" ;
    
    // auto login 
    if ( validSession()) {
        header("Location: userPage.php") ;
        exit ;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">

    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">

    <meta name="robots" content="noindex, follow">
    </head>
    <body>


    <?php
        // Authentication
        if ( !empty($_POST)) {
            extract($_POST) ;
            if ( checkUser($email, $pass) ) {
                // the user is authenticated
                // Store data to use in other php files. 
                $_SESSION["user"] = getUser($email) ;
                header("Location: userPage.php") ; // redirect to main page
                exit ;
            }
            $authError = true ;
        }
    ?>


    <div class="limiter">
    <div class="container-login100">
    <div class="wrap-login100 p-t-50 p-b-90">
    <form action="?" method="post">
    <span class="login100-form-title p-b-51">
    Login
    </span>


    <div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
    <input class="input100" type="text" name="email" placeholder="Email" value="<?= $email ?? '' ?>">
    </div>
    <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
    <input class="input100" type="password" name="pass" placeholder="Password">
    <span class="focus-input100"></span>
    </div>
    <div class="flex-sb-m w-full p-t-3 p-b-24">
    <div class="contact100-form-checkbox">
    <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
    </div>
    </div>
    <div class="container-login100-form-btn m-t-17">
    <button class="login100-form-btn">
    Login
    </button>
    <?php   
        // Authentication Error Message
        if( isset($authError)) {
            echo "<p class='error' style='margin-top:20px;'>Wrong email or password</p>" ;
        }

        // Direct access to main page error message
        if ( isset($_GET["error"])) {
            echo "<p class='error'>You tried to access main.php directly</p>" ;
        }

    ?>

    </div>
    </form>
    </div>
    </div>
    </div>

    <script src="js/main.js"></script>

    <button class="btn-shine" style="position: fixed; bottom: 0; right: 0; padding:15px; margin:10px;">
    <span><a style="text-decoration:none; color:inherit;" href="mainPage.php">Main Page</a></span>
    </button>

    </body>
    </html>
