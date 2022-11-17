<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <script src="../../js/dashboard.js"></script>
        <link rel="icon" href="../../css/favicon.png" sizes="any">
        <title>Account | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Account</h1>
            <h4>Manage your Resonance account.</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h3>Account Info</h2>
            <h4>Username: <?php echo $user['username']; ?></h3>
            <h4>Email: <?php echo $user['email']; ?></h3>
            <h4>License: <?php echo $user['license']; ?></h3>
            <h4>HWID: <?php if(!is_null($user['hwid'])){ echo $user['hwid']; }else{ echo "N/A"; } ?></h3>
            <h4>Last Login: <?php echo $user['lastlogin']; ?></h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h3>HWID Reset</h3>
            <h4>Last HWID Reset: <?php if(!is_null($user['hwidreset'])){ echo $user['hwidreset']; }else{ echo "N/A"; } ?></h4>
            <h4><?php if(!is_null($user['hwid'])){ if(!is_null($user['hwidreset'])){ if(strtotime(date('Y-m-d H:i:s')) > strtotime("+7 day", strtotime($user['hwidreset']))){ echo "<a href='resethwid.php'>[Reset HWID]</a>"; }else{ echo "You must wait 7 days from your last reset"; } }else{ echo "<a href='resethwid.php'>[Reset HWID]</a>"; } }else{ echo "You cannot reset your HWID right now."; } ?></h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h3>Password Reset</h3>
            <h4>Last Password Reset: <?php if(!is_null($user['passwordreset'])){ echo $user['passwordreset']; }else{ echo "N/A"; } ?></h4>
            <h4 style="cursor: pointer;" onclick="changePassword();">[Change Password]</h4>
        </div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/account">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/account">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>