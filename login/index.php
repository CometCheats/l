<?php

    require('../php/db.php'); // MySQL connection
    require('../php/user.php'); // Various user account functions
    require('../php/tools.php');

    if(isLoggedIn()){
        header("location: ../dashboard/"); // Redirect to dashboard if already logged in
        exit;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="icon" href="../css/favicon.png" sizes="any">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login | Resonance Cheats</title>
    </head>
    <body>
        <div class="login-header">
            <h1>Login</h1>
            <h2>Access your Resonance Cheats account</h2>
        </div>
        <div class="login-form">
            <form action="./login.php" method="POST">
                <input type="text" name="username" placeholder="Username/Email"><br><br>
                <input type="password" name="password" placeholder="Password"><br><br>
                <div class="h-captcha" data-sitekey="0b576cf1-eea6-4139-bb6c-19a9cf20834b" data-theme="dark"></div><br><br>
                <button class="login-button" type="submit">Login</button><br><br>
                <?php 
                    if(isset($_SESSION['loginfailedreason'])){
                        echo "<span style=\"color:red;\">".$_SESSION['loginfailedreason']."</span><br><br>";     
                        unset($_SESSION['loginfailedreason']); 
                    } 
                ?>
                <a href="forgotpassword.php">
                    <span>Forgot your Password?</span>
                </a>
            </form>
        </div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../privacy/?ref=login">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../tos/?ref=login">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</html>