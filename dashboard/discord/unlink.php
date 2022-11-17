<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(!is_null($user["discordid"]) && !is_null($user["discordoauth"])){
        $curl = curl_init();
        $post = [
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'token'   => $user["discordoauth"],
        ];
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://discord.com/api/oauth2/token/revoke",
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

        $post = [
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'token'   => $user["discordoauth"],
        ];
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://discord.com/api/oauth2/token/revoke",
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
        curl_close($curl);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://discord.com/api/guilds/".DISCORD_ID."/members/".$jResponse2->id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bot ".DISCORD_BOT_TOKEN.""
            ],
        ]);
        curl_exec($curl);
        curl_close($curl);

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
        exit;
    }else{
        header("location: ./");
        exit;
    }

?>