<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM downloads WHERE public="1" ORDER BY priority');
    $stmt->execute();
    $downloadsResults = $stmt->get_result();

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <script src="../../../js/dashboard.js"></script>
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>Downloads | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Downloads</h1>
            <h4>View all the files we offer for download.</h4>
        </div>
        <div class="divider"></div>
        <?php while($downloadsRow = $downloadsResults->fetch_assoc()){ ?>
            <div class="dashboard-header">
                <?php if((bool)$downloadsRow["enabled"]){ ?>
                    <a href="download.php?id=<?php echo $downloadsRow["id"]; ?>">
                <?php }else { ?>
                    <div onclick="alert('This download is currently disabled');">
                <?php } ?>
                <h1><?php echo $downloadsRow["name"]; ?></h1>
                <h4><?php echo $downloadsRow["description"]; ?></h4>
                <?php if((bool)$downloadsRow["enabled"]){ ?>
                    </a>
                <?php }else{ ?>
                    </div>
                <?php } ?>
            </div>  
        <div class="divider"></div>
        <?php } ?>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/downloads">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/downloads">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>