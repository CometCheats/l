<?php
    require('db.php');
    require('tools.php');
    require( 'ssp.class.php' );
    requireLogin();
    requireAdmin();

    $sql_details = array(
        'user' => DB_USERNAME,
        'pass' => DB_PASSWORD,
        'db'   => DB_NAME,
        'host' => DB_SERVER
    );

    switch($_REQUEST["source"]){
        case "users":
            $stmt = $link->prepare('SELECT * FROM users');
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberOfUsers = $stmt->num_rows;
            $table = 'users';
            $primaryKey = 'id';
            $columns = array(
                array( 'db' => 'username', 'dt' => 0 ),
                array( 'db' => 'email',  'dt' => 1 ),
                array( 'db' => 'license',   'dt' => 2 ),
                array( 'db' => 'discordoauth',     'dt' => 3 ),
                array( 'db' => 'discordbanned',     'dt' => 4 ),
                array( 'db' => 'hwidreset',     'dt' => 5 ),
                array( 'db' => 'lastlogin',     'dt' => 6 ),
                array( 'db' => 'created',     'dt' => 7 ),
                array( 'db' => 'banned',     'dt' => 8 )
            );
            $response = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
            $finalResponse = array(
                "draw" => $response["draw"],
                "recordsTotal" => $response["recordsTotal"],
                "recordsFiltered" => $response["recordsFiltered"],
                "data" => array()
            );
            foreach($response["data"] as $user){
                if(is_null($user[3])){
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
                            "authorization: Bearer ".$user[3]
                        ],
                    ]);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $oauthResponse = json_decode($response);
                    $discord = $oauthResponse->username."#".$oauthResponse->discriminator;
                }
                if((bool)$user[4]){
                    $discordBanned = "Yes";
                }else{
                    $discordBanned = "No";
                }
                if(is_null($user[5])){
                    $lastHWIDReset = "N/A";
                }else{
                    $lastHWIDReset = $user[5];
                }
                if(is_null($user[6])){
                    $lastLogin = "N/A";
                }else{
                    $lastLogin = $user[6];
                }
                if((bool)$user[8]){
                    $banned = "Yes";
                }else{
                    $banned = "No";
                }
                $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
                $stmt->bind_param('s', $user[0]);
                $stmt->execute();
                $result = $stmt->get_result();
                $license = $result->fetch_assoc();
                $manageLink = "<a href='manage/?id=".$license["id"]."'>Manage</a>";
                array_push($finalResponse["data"], array(
                    $user[0],
                    $user[1],
                    $user[2],
                    $discord,
                    $discordBanned,
                    $lastHWIDReset,
                    $lastLogin,
                    $user[7],
                    $banned,
                    $manageLink
                ));
            }
            echo json_encode($finalResponse);
        break;
        case "licenses":
            $stmt = $link->prepare('SELECT * FROM licensekeys');
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberOfUsers = $stmt->num_rows;
            $table = 'licensekeys';
            $primaryKey = 'id';
            $columns = array(
                array( 'db' => 'license', 'dt' => 0 ),
                array( 'db' => 'creator',  'dt' => 1 ),
                array( 'db' => 'created',   'dt' => 2 ),
                array( 'db' => 'redeemed',     'dt' => 3 ),
                array( 'db' => 'redeemdate',     'dt' => 4 ),
                array( 'db' => 'user',     'dt' => 5 ),
            );
            $response = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
            $finalResponse = array(
                "draw" => $response["draw"],
                "recordsTotal" => $response["recordsTotal"],
                "recordsFiltered" => $response["recordsFiltered"],
                "data" => array()
            );
            foreach($response["data"] as $user){
                $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
                $stmt->bind_param('s', $user[1]);
                $stmt->execute();
                $result = $stmt->get_result();
                $creator = $result->fetch_assoc();
                $creator = $creator["username"];
                if((bool)$user[3]){
                    $redeemed = "Yes";
                }else{
                    $redeemed = "No";
                }
                if(is_null($user[4])){
                    $redeemedDate = "N/A";
                }else{
                    $redeemedDate = $user[4];
                }
                if(is_null($user[5])){
                    $redeemedUser = "N/A";
                }else{
                    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
                    $stmt->bind_param('s', $user[5]);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $redeemedUser = $result->fetch_assoc();
                    $redeemedUser = $redeemedUser["username"];
                }
                $stmt = $link->prepare('SELECT * FROM licensekeys WHERE license=?');
                $stmt->bind_param('s', $user[0]);
                $stmt->execute();
                $result = $stmt->get_result();
                $license = $result->fetch_assoc();
                $manageLink = "<a href='manage/?id=".$license["id"]."'>Manage</a>";
                array_push($finalResponse["data"], array(
                    $user[0],
                    $creator,
                    $user[2],
                    $redeemed,
                    $redeemedDate,
                    $redeemedUser,
                    $manageLink
                ));
            }
            echo json_encode($finalResponse);
        break;
        case "sessions":
            $table = 'sessions';
            $primaryKey = 'id';
            $columns = array(
                array( 'db' => 'user', 'dt' => 0 ),
                array( 'db' => 'ip',  'dt' => 1 ),
                array( 'db' => 'gameName',   'dt' => 2 ),
                array( 'db' => 'created',     'dt' => 3 ),
                array( 'db' => 'created',     'dt' => 4 ),
                array( 'db' => 'id',     'dt' => 5 ),
            );
            $response = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
            $finalResponse = array(
                "draw" => $response["draw"],
                "recordsTotal" => $response["recordsTotal"],
                "recordsFiltered" => $response["recordsFiltered"],
                "data" => array()
            );
            foreach($response["data"] as $session){
                $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
                $stmt->bind_param('s', $session[0]);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $functionName = randomName();
                $elapsed = strtotime(date('Y-m-d H:i:s')) - strtotime($session[4]);
                $hours = 0;
                $minutes = 0;
                $seconds = 0;
                if($elapsed >= 3600){
                    $hours = intdiv($elapsed, 3600);
                    $elapsed = $elapsed-(3600*$hours);
                }
                if($elapsed >= 60){
                    $minutes = intdiv($elapsed, 60);
                    $elapsed = $elapsed-(60*$minutes);
                }
                $seconds = $elapsed;
                $coolJavascript = "
                <div id=\"".$functionName."\"></div>
                <script>
                var ".$functionName."First = true;
                var ".$functionName."h = 0;
                var ".$functionName."m = 0;
                var ".$functionName."s = 0;
                function ".$functionName."() {
                    const today = new Date();
                    if(".$functionName."First){
                        ".$functionName."h = ".$hours.";       
                        ".$functionName."m = ".$minutes.";       
                        ".$functionName."s = ".$seconds.";                       
                    ".$functionName."First = false;
                    }else{
                    if(".$functionName."s+1 > 60){
                        ".$functionName."s = 0;
                        if(".$functionName."m+1 > 60){
                            ".$functionName."m = 0; ".$functionName."h++;
                        }else{
                        ".$functionName."m++;
                        }
                    }else{
                        ".$functionName."s++;
                    }
                    if(".$functionName."m+1 > 60){
                        ".$functionName."m = 0; ".$functionName."h++;
                    }
                    }
                    document.getElementById('".$functionName."').innerHTML =  ".$functionName."h + \":\" + ".$functionName."m + \":\" + ".$functionName."s;
                    setTimeout(".$functionName.", 1000);
                }
                $(document).ready( function () {
                    ".$functionName."();
                });
                </script>
                ";
                $endLink = "<a href='end/?id=".$session[5]."'>End Session</a>";
                array_push($finalResponse["data"], array(
                    $user['username'],
                    $session[1],
                    $session[2],
                    $session[3],
                    $coolJavascript,
                    $endLink
                ));
            }
            echo json_encode($finalResponse);
        break;
    }

?>


    
