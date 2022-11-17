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
        <title>Register | Resonance Cheats</title>
    </head>
    <body>
        <div class="login-header">
            <h1>Register</h1>
            <h2>Create a Resonance Cheats account</h2>
        </div>
        <div class="login-form">
            <form action="./register.php" method="POST">
                <input type="text" name="username" placeholder="Username" required><br><br>
                <input type="email" name="email" placeholder="Email" required><br><br>
                <input type="text" name="license" placeholder="License Key" required><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
                <div class="h-captcha" data-sitekey="0b576cf1-eea6-4139-bb6c-19a9cf20834b" data-theme="dark"></div><br><br>
                <button class="login-button" type="submit">Register</button><br><br>
                <?php 
                    if(isset($_SESSION['registerfailedreason'])){
                        echo "<span style=\"color:red;\">".$_SESSION['registerfailedreason']."</span><br><br>";     
                        unset($_SESSION['registerfailedreason']); 
                    } 
                ?>
                <a href="../login/">
                    <span>Already have an account?</span>
                </a>
            </form>
        </div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../privacy/?ref=register">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../tos/?ref=register">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</html>