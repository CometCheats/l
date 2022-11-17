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
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberofrows = $stmt->num_rows;
    if($numberofrows < 1){
        header("location: ../");
        exit;
    }

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $selecteduser = $result->fetch_assoc();

    if(is_null($selecteduser['discordid'])){
        $discord = "Not Linked";
    }else{
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
                "authorization: Bearer ".$selecteduser['discordoauth']
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $oauthResponse = json_decode($response);
        $discord = $oauthResponse->username."#".$oauthResponse->discriminator;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../../css/style.css">
        <link rel="icon" href="../../../../css/favicon.png" sizes="any">
        <title>Users | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    <div class="dashboard-header">
            <h1>Manage User</h1>
            <h4>Currently managing user: <?php echo $selecteduser['username']; ?></h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>User Info</h2>
            <h3>Username: <?php echo $selecteduser['username']; ?></h3>
            <h3>Email: <?php echo $selecteduser['email']; ?></h3>
            <h3>License: <?php echo $selecteduser['license']; ?></h3>
            <h3>Tester: <?php if((bool)$selecteduser['tester']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>Admin: <?php if((bool)$selecteduser['admin']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>Super Admin: <?php if((bool)$selecteduser['superadmin']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>Authentications: <?php echo $selecteduser['authentications']; ?></h3>
            <h3>Hours using Resonance: <?php echo round(($selecteduser['time']/60)/60, 2); ?></h3>
            <h3>Discord: <?php echo $discord; ?></h3>
            <h3>Discord Banned: <?php if((bool)$selecteduser['discordbanned']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>Last password reset: <?php if(is_null($selecteduser['passwordreset'])){ echo "N/A"; }else{ echo $selecteduser['passwordreset']; } ?></h3>
            <h3>HWID: <?php if(is_null($selecteduser['hwid'])){ echo "N/A"; }else{ echo $selecteduser['hwid']; } ?></h3>
            <h3>Last HWID reset: <?php if(is_null($selecteduser['hwidreset'])){ echo "N/A"; }else{ echo $selecteduser['hwidreset']; } ?></h3>
            <h3>Last login: <?php if(is_null($selecteduser['lastlogin'])){ echo "N/A"; }else{ echo $selecteduser['lastlogin']; } ?></h3>
            <h3>Created: <?php echo $selecteduser['created']; ?></h3>
            <h3>Banned: <?php if((bool)$selecteduser['banned']){ echo "Yes"; }else{ echo "No"; } ?></h3>
            <h3>ID: <?php echo $selecteduser['id']; ?></h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Actions</h2>
            <h3><a href="setrank.php?rank=tester&id=<?php echo $selecteduser['id']; ?>"><?php if((bool)$selecteduser['tester']){ echo "Remove Tester"; }else{ echo "Give Tester"; } ?></a></h3>
            <h3><a href="setrank.php?rank=admin&id=<?php echo $selecteduser['id']; ?>"><?php if((bool)$selecteduser['admin']){ echo "Remove Admin"; }else{ echo "Give Admin"; } ?></a></h3>
            <h3><a href="setrank.php?rank=superadmin&id=<?php echo $selecteduser['id']; ?>"><?php if((bool)$selecteduser['superadmin']){ echo "Remove Super Admin"; }else{ echo "Give Super Admin"; } ?></a></h3>
            <h3><a href="unlinkdiscord.php?id=<?php echo $selecteduser['id']; ?>">Unlink Discord</a></h3>
            <h3><a href="discordbanuser.php?id=<?php echo $selecteduser['id']; ?>"><?php if((bool)$selecteduser['discordbanned']){ echo "Un-Discord Ban User"; }else{ echo "Discord-Ban User"; } ?></a></h3>
            <h3><a href="resethwid.php?id=<?php echo $selecteduser['id']; ?>">Reset HWID</a></h3>
            <h3><a href="banuser.php?id=<?php echo $selecteduser['id']; ?>"><?php if((bool)$selecteduser['banned']){ echo "Unban User"; }else{ echo "Ban User"; } ?></a></h3>
        </div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../../../privacy/?ref=dashboard/admin/users/manage?id=<?php echo $_GET['id']; ?>">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../../../../tos/?ref=dashboard/admin/users/manage?id=<?php echo $_GET['id']; ?>">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>