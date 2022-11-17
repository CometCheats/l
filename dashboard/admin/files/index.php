<?php

    require('../../../php/db.php');
    require('../../../php/tools.php');
    requireLogin();
    requireAdmin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM downloads ORDER BY priority');
    $stmt->execute();
    $downloadsResults = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>Files | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Files</h1>
            <h4>Here you can manage Resonance Files.</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="newfile/">
                <h2>New File</h2>
                <h3>Upload a new file.</h3>
            </a>
        </div>
        <?php while($downloadsRow = $downloadsResults->fetch_assoc()){ ?>
        <div class="divider"></div>
        <div class="dashboard-header">
            <a href="manage/?id=<?php echo $downloadsRow['id']; ?>">
                <h2><?php echo $downloadsRow['name']; ?></h2>
                <h3>[Manage File]</h3>
            </a>
        </div>
        <?php } ?>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/admin/files">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/admin/files">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>