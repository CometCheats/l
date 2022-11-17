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
            <h4>Here you can generate licenses for Resonance.</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <center>
                <h2>Generate</h2>
                <div class="login-form">
                    <form action="generate.php" method="POST">
                        <input type="text" name="prefix" placeholder="Prefix" value="RETAIL" required><br><br>
                        <input type="number" name="quantity" placeholder="Quantity" min="1" max="99" required><br><br>
                        <input type="hidden" name="token" value="<?php $token = rand(); $_SESSION['keyGenerationToken'] = $token; echo $token; ?>">
                        <button class="login-button" type="submit">Generate Key(s)</button><br><br>
                    </form>
                </div>
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