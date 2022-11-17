<?php

    require('../../../../php/db.php');
    require('../../../../php/tools.php');
    requireLogin();
    requireAdmin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(!isset($_GET['id'])){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM licensekeys WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberofrows = $stmt->num_rows;
    if($numberofrows < 1){
        header("location: ../");
        exit;
    }

    $stmt = $link->prepare('SELECT * FROM licensekeys WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $key = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $key['creator']);
    $stmt->execute();
    $result = $stmt->get_result();
    $keycreator = $result->fetch_assoc();

    if((bool)$key['redeemed']){
        $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
        $stmt->bind_param('s', $key['user']);
        $stmt->execute();
        $result = $stmt->get_result();
        $keyuser = $result->fetch_assoc();
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../../css/style.css">
        <link rel="icon" href="../../../../css/favicon.png" sizes="any">
        <title>Licenses | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    <div class="dashboard-header">
            <h1>Manage License</h1>
            <h4>Currently managing license: <?php echo $key['license']; ?></h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>License Info</h2>
            <h3>License: <?php echo $key['license']; ?></h3>
            <h3>Creator: <?php echo $keycreator['username']; ?></h3>
            <h3>Created: <?php echo $key['created']; ?></h3>
            <h3>Redeemed: <?php if((bool)$key['redeemed']){ echo "Yes"; }else{ echo "No"; }?></h3>
            <?php
                if((bool)$key['redeemed']){
            ?>
            <h3>Redeem Date: <?php echo $key['redeemdate']; ?></h3>
            <h3>Redeem User: <a href="../../users/manage/?id=<?php echo $keyuser['id']; ?>"><?php echo $keyuser['username']; ?></a></h3>
            <?php
                }
            ?>
            <h3>ID: <?php echo $key['id']; ?></h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Actions</h2>
            <h3><a href="deletekey.php?id=<?php echo $key['id']; ?>">Delete key</a></h3>
        </div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../../../privacy/?ref=dashboard/admin/licenses/manage?id=<?php echo $_GET['id']; ?>">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../../../../tos/?ref=dashboard/admin/licenses/manage?id=<?php echo $_GET['id']; ?>">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>