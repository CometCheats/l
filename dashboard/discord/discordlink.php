<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(isset($_GET['code'])){
        if((bool)$user["discordbanned"]){
            header("location: ./");
        }
        if(!is_null($user["discordid"]) || !is_null($user["discordoauth"])){
            header("location: ./");
        }   

        $curl = curl_init();
        $post = [
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'grant_type'   => 'authorization_code',
            'code' => $_GET['code'],
            'redirect_uri' => 'https://resonancecheats.com/dashboard/discord/discordlink.php'
        ];
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://discord.com/api/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => [
              "Content-Type: multipart/form-data;boundary="
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $jResponse = json_decode($response);
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
                "authorization: Bearer ".$jResponse->access_token
            ],
        ]);
        $response2 = curl_exec($curl);
        curl_close($curl);
        $jResponse2 = json_decode($response2);

        $payload = json_encode( array( "access_token" => $jResponse->access_token ) );

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://discord.com/api/guilds/".DISCORD_ID."/members/".$jResponse2->id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bot ".DISCORD_BOT_TOKEN.""
            ],
        ]);
        curl_exec($curl);
        curl_close($curl);


        $stmt = $link->prepare("UPDATE users SET discordid=? WHERE id=?");
        $stmt->bind_param("ss", $jResponse2->id, $user['id']);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("UPDATE users SET discordoauth=? WHERE id=?");
        $stmt->bind_param("ss", $jResponse->access_token, $user['id']);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("UPDATE users SET discordoauthrefresh=? WHERE id=?");
        $stmt->bind_param("ss", $jResponse->refresh_token, $user['id']);
        $stmt->execute();
        $stmt->close();
        $expiration = date("Y-m-d H:i:s ", time() + 518400);
        $stmt = $link->prepare("UPDATE users SET discordrefresh=? WHERE id=?");
        $stmt->bind_param("ss", $expiration, $user['id']);
        $stmt->execute();
        $stmt->close();

        header("location: ./");
        exit;

    }else{
        header("location: ./");
        exit;
    }

?>