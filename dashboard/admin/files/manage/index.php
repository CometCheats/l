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
    $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberofrows = $stmt->num_rows;
    if($numberofrows < 1){
        header("location: ../");
        exit;
    }

    $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $download = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../../css/style.css">
        <link rel="icon" href="../../../../css/favicon.png" sizes="any">
        <title>Files | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    <div class="dashboard-header">
            <h1>Manage File</h1>
            <h4>Currently managing file: <?php echo $download['name']; ?></h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Download Info</h2>
            <h3>Name: <?php echo $download['name']; ?></h3>
            <h3>Description: <?php echo $download['description']; ?></h3>
            <h3>File Name: <?php echo $download['filename']; ?></h3>
            <h3>Friendly File Name: <?php echo $download['frontFilename']; ?></h3>
            <h3>Checksum: <?php echo $download['checksum']; ?></h3>
            <h3>Path: <?php echo $download['path']; ?></h3>
            <h3>Priority: <?php echo $download['priority']; ?></h3>
            <h3>ID: <?php echo $download['id']; ?></h3>
            <h3>Enabled: <?php if((bool)$download['enabled']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>Public: <?php if((bool)$download['public']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>Injector: <?php if((bool)$download['injector']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>No Edit (Injector): <?php if((bool)$download['noEdit']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Actions</h2>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="file" require>
                <input type="hidden" name="id" value="<?php echo $download['id']; ?>">
                <input type="submit" value="Upload">
            </form>
            <h3><a href="setStatus.php?type=enabled&id=<?php echo $download['id']; ?>"><?php if((bool)$download['enabled']){ echo "Disable File"; }else{ echo "Enable File"; } ?></a></h3>
            <h3><a href="setStatus.php?type=public&id=<?php echo $download['id']; ?>"><?php if((bool)$download['public']){ echo "Set File Private"; }else{ echo "Set File Public"; } ?></a></h3>
            <h3><a href="setStatus.php?type=injector&id=<?php echo $download['id']; ?>"><?php if((bool)$download['injector']){ echo "Remove File From Injector"; }else{ echo "Add File To Injector"; } ?></a></h3>
            <h3><a href="setStatus.php?type=noEdit&id=<?php echo $download['id']; ?>"><?php if((bool)$download['noEdit']){ echo "Disable No Edit"; }else{ echo "Enable No Edit"; } ?></a></h3>
        </div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../../../privacy/?ref=dashboard/admin/files/manage?id=<?php echo $_GET['id']; ?>">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../../../../tos/?ref=dashboard/admin/files/manage?id=<?php echo $_GET['id']; ?>">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>