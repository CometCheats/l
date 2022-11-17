<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();
    requireAdmin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM information WHERE id="resonance"');
    $stmt->execute();
    $result = $stmt->get_result();
    $information = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM users');
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberOfUsers = $stmt->num_rows;

    $stmt = $link->prepare('SELECT * FROM licensekeys');
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $totalNumberOfKeys = $stmt->num_rows;

    $stmt = $link->prepare('SELECT * FROM licensekeys WHERE redeemed=0');
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $unredeemedKeys = $stmt->num_rows;

    $stmt = $link->prepare('SELECT * FROM licensekeys WHERE redeemed=1');
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $redeemedKeys = $stmt->num_rows;

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="icon" href="../../css/favicon.png" sizes="any">
        <title>Admin | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Admin</h1>
            <h4>Here you can manage Resonance.</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Statistics</h2>
            <h3>| Users: <?php echo $numberOfUsers; ?> | Total Keys: <?php echo $totalNumberOfKeys; ?> | Unredeemed Keys: <?php echo $unredeemedKeys; ?> | Redeemed Keys: <?php echo $redeemedKeys; ?> | Percent Unredeemed: <?php echo round(($unredeemedKeys/$totalNumberOfKeys)*100, 0); ?>% | Percent Redeemed: <?php echo round(($redeemedKeys/$totalNumberOfKeys)*100, 0); ?>% |<br>| Total Authentications: <?php echo $information['authentications']; ?> | Total Resonance Play Time: <?php echo round(($information['time']/60)/60, 2); ?> Hours |</h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="users/">
                <h2>Users</h2>
                <h3>Manage Resonnace users.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="licenses/">
                <h2>License Keys</h2>
                <h3>Manage Resonnace license keys.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="licensegenerator/">
                <h2>License Generator</h2>
                <h3>Generate Resonnace license keys.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="sessions/">
                <h2>Sessions</h2>
                <h3>Manage current Resonance Sessions.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="files/">
                <h2>File Manager</h2>
                <h3>Manage Resonance Files.</h3>
            </a>
        </div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/admin">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/admin">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>