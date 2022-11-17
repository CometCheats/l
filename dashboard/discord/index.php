<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://discord.com/api/v9/users/@me",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => [
            "authorization: Bearer ".$user["discordoauth"]
        ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    $oauthResponse = json_decode($response);
    if(isset($oauthResponse->message) && !is_null($user["discordid"]) && !is_null($user["discordoauth"])){
        if($oauthResponse->message == "401: Unauthorized"){
            $stmt = $link->prepare("UPDATE users SET discordid=NULL WHERE id=?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();
            $stmt->close();
            $stmt = $link->prepare("UPDATE users SET discordoauth=NULL WHERE id=?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();
            $stmt->close();
            $stmt = $link->prepare("UPDATE users SET discordoauthrefresh=NULL WHERE id=?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();
            $stmt->close();
            header("location: ./");
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <script src="../../../js/dashboard.js"></script>
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>Discord | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Discord</h1>
            <h4>Link your Discord account to your Resonance account. We use this system to prevent spam in the Discord.</h4>
        </div>
        <div class="divider"></div>
            <center>
                <?php if((bool)$user["discordbanned"]){ ?>
                <h1>Your Resonance account was banned from the community Discord</h1>
                <?php }else{ ?>
                <?php if(is_null($user["discordid"]) || is_null($user["discordoauth"])){ ?>
                <h1>No Discord Linked</h1>
                <a href="https://discord.com/api/oauth2/authorize?client_id=945808242917904414&redirect_uri=https%3A%2F%2Fresonancecheats.com%2Fdashboard%2Fdiscord%2Fdiscordlink.php&response_type=code&scope=guilds%20guilds.join%20identify"><h4>Link here</h4></a>
                <?php }else{ ?>
                <br>
                <img style="border-radius: 50%;border: 2px solid white;" src="https://cdn.discordapp.com/avatars/<?php echo $oauthResponse->id; ?>/<?php echo $oauthResponse->avatar ?>">
                <h1><?php echo $oauthResponse->username."#".$oauthResponse->discriminator; ?></h1>
                <a href="./unlink.php"><h4>Unlink</h4></a>
                <br>
                <?php } ?>
                <?php } ?>
            </center>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/discord">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/discord">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>