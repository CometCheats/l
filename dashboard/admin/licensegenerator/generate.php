<?php

    require('../../../php/db.php');
    require('../../../php/tools.php');
    require('../../../php/audit.php');
    requireLogin();
    requireAdmin();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(isset($_POST['prefix']) && isset($_POST['quantity']) && isset($_POST['token'])){
        if($_POST['token'] == $_SESSION['keyGenerationToken']){
            if(is_numeric($_POST['quantity'])){
                $allkeys = "";
                for($keyrun=0;$keyrun<$_POST['quantity'];$keyrun++){
                    $key = $_POST['prefix']."-";
                    $keyid = uuid();
                    for($x=0;$x<16;$x++){
                        $randomNumbers = rand(48,57);
                        $randomLetters = rand(65,90);
                        $toUse = (rand(0,1));
                        if($toUse){
                            $key = $key.chr($randomNumbers);
                        }else{
                            $key = $key.chr($randomLetters);
                        }
                        if(($x + 1 == 4) || ($x + 1 == 8) || ($x + 1 == 12)){
                            $key = $key."-";
                        }
                    }
                    $stmt = $link->prepare("INSERT INTO licensekeys (license, creator, id) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $key, $user['id'], $keyid);
                    $stmt->execute();
                    $stmt->close();
                    $allkeys = $allkeys."<h3>".$key."</h3>\n";
                }
                audit("ADMIN_KEY_GENERATOR (".$_POST['quantity'].")", $user['id'], "SUCCESSFUL_GENERATION"); 
                unset($_SESSION['keyGenerationToken']);
            }else{
                header("location: ./");
                exit;  
            }
        }else{
            header("location: ./");
            unset($_SESSION['keyGenerationToken']);
            exit;  
        }
    }else{
        header("location: ./");
        exit;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>License Generator | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>License Generator</h1>
            <h4>You've generated <?php echo $_POST['quantity']; ?> key(s).</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <center>
                <h2>Keys Generated:</h2>
                <?php echo $allkeys; ?>
            </center>
        </div>
        <div class="divider"></div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/admin/licensegenerator">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/admin/licensegenerator">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>