<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'Upload.php';
    require_once 'userDb.php';

    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $birthDate = $_POST['birth_date'];

    $regex = '/(\w+)@((?:\w+\.){1,3}(?:com|tr))/iu' ;
    // Upload profile picture
    $profilePicture = new Upload("pp", "images");

    $a=0;
    $b=0;

    if ($profilePicture->error) {
        $a++;
    } 

    if (!(preg_match_all($regex,$email))) {
        $b++;
    } 

    if($a!==0 || $b!==0){
       
        
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


        if (strlen($password) < 5 || strlen($password) > 15) {
            // echo "Error: Password length should be between 5 and 15 characters.";
        }
        else{
        // Insert user data into the database
        $stmt = $db->prepare("INSERT INTO users (email, password, name, surname, pp, birth_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $name, $surname, $profilePicture->filename, $birthDate]);
        // Redirect to a success page or display a success message
        header("Location: success.php");
        exit;
        }
    }  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <style>
        /* Add any additional styles specific to this page */
    </style>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 p-t-50 p-b-90">
                <form action="" method="post" enctype="multipart/form-data">
                    <span class="login100-form-title p-b-51">
                        Register
                    </span>
                    <div class="wrap-input100 validate-input m-b-16" data-validate="Email is required">
                        <input class="input100" type="text" name="email" placeholder="Email" value="<?= $email ?? "" ?>">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
                        <input class="input100" type="password" name="pass" placeholder="Password">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate="Name is required">
                        <input class="input100" type="text" name="name" placeholder="Name" value="<?= $name ?? "" ?>">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate="Surname is required">
                        <input class="input100" type="text" name="surname" placeholder="Surname" value="<?= $surname ?? "" ?>">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate="Birth date is required">
                        <input class="input100" type="date" name="birth_date" value="<?= $birthDate ?? "" ?>">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate="Profile Picture is required">
                        <input class="input100" type="file" name="pp" value="<?= $pp ?? "" ?>">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="container-login100-form-btn m-t-17">
                        <button class="login100-form-btn">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
    <?php
     if(isset($a) && $a>0){
        echo "<p class='error' style='margin-left:37%;'>Error: " . $profilePicture->error . "</p>";
    }
    if(isset($a) && $b>0){
        echo "<p class='error' style='margin-left:37%;'>Error: Email is invalid. Please enter a valid email.</p>";
    }

    if ((isset($password)) && (strlen($password) < 5 || strlen($password) > 15)) {
        // echo "Error: Password length should be between 5 and 15 characters.";
        echo "<p class='error' style='margin-left:37%;'>Error:  Password length should be between 5 and 15 characters.</p>";
    }
    
    ?>

<button class="btn-shine" style="position: fixed; bottom: 0; right: 0; padding:15px; margin:10px;">
    <span><a style="text-decoration:none; color:inherit;" href="mainPage.php">Main Page</a></span>
    </button>
</body>
</html>