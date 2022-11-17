<?php

    require('../php/db.php');
    require('../php/tools.php');
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
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="icon" href="../css/favicon.png" sizes="any">
        <title>Dashboard | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Welcome, <?php echo $user['username']; ?></h1>
            <h4>Here you can manage your Resonance Cheats account.</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="account/">
                <h2>Manage your Account</h2>
                <h3>View and edit properties of your account.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="discord/">
                <h2>Discord</h2>
                <h3>Link your Discord account to get access to the community discord.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="downloads/">
                <h2>Downloads</h2>
                <h3>Access the files needed to use your Resonance Cheat Products.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="faq/">
                <h2>FAQ</h2>
                <h3>Get answers to frequently asked questions.</h3>
            </a>
        </div>
        <?php if((bool)$user["admin"]){ ?>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="admin/">
                <h2>Admin</h2>
                <h3>Manage the entire Resonance Cheats network.</h3>
            </a>
        </div>
        <?php } ?>
        <div class="divider"></div>
        <br><br><br>
        <footer>
        <span> | </span>
            <a href="logout.php">
                <span>Logout</span> 
            </a>    
            <span> | </span>
            <a href="../privacy/?ref=dashboard">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../tos/?ref=dashboard">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>