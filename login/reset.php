<?php

    require('../php/db.php'); // MySQL connection
    require('../php/user.php'); // Various user account functions
    require('../php/audit.php'); // Auditing
    require('../php/tools.php'); //General tools

    if(isLoggedIn()){
        header("location: ../dashboard/"); // Redirect to dashboard if already logged in
        exit;
    }

    $userid = NULL; // To be used once we have verified the account exists
    
    if(isset($_GET['code'])){
        $stmt = $link->prepare('SELECT * FROM confirmations WHERE code=?');
        $stmt->bind_param('s', $_GET['code']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows < 1){
            $_SESSION['registerfailedreason'] = "Code not found"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
            audit("CLIENT_REGISTER_CONFIRMATION", NULL, "USER_CODE_NOT_FOUND"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }
        $stmt = $link->prepare('SELECT * FROM confirmations WHERE code=?');
        $stmt->bind_param('s', $_GET['code']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $userid = $row['user'];
        $confirmationid = $row['id'];
        
    }else{
        header("location: ./");
        exit;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="icon" href="../css/favicon.png" sizes="any">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password | Resonance Cheats</title>
    </head>
    <body>
        <div class="login-header">
            <h1>Reset password</h1>
            <h2>Reset your Resonance Cheats Password</h2>
        </div>
        <div class="login-form">
            <form action="./finalreset.php" method="POST">
                <input type="password" name="password" placeholder="New Password"><br><br>
                <input type="hidden" name="code" value="<?php echo $_GET['code']; ?>">
                <button class="login-button" type="submit">Reset Password</button><br><br>
            </form>
        </div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../privacy/?ref=login/reset.php?code=<?php echo $_GET['code']; ?>">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../tos/?ref=login/reset.php?code=<?php echo $_GET['code']; ?>">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</html>