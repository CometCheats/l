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
        <title>Forgot Password | Resonance Cheats</title>
    </head>
    <body>
        <div class="login-header">
            <h1>Forgot password</h1>
            <h2>Reset your Resonance Cheats Password</h2>
        </div>
        <div class="login-form">
            <form action="./startreset.php" method="POST">
                <input type="text" name="email" placeholder="Email"><br><br>
                <div class="h-captcha" data-sitekey="0b576cf1-eea6-4139-bb6c-19a9cf20834b" data-theme="dark"></div><br><br>
                <button class="login-button" type="submit">Reset Password</button><br><br>
                <span>Enter the email associated with your account. If the account exists you will recieve an email with instructions on how to reset your password.</span>
            </form>
        </div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../privacy/?ref=resetpassword">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../tos/?ref=resetpassword">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</html>