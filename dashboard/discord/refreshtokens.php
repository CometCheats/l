<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    require('../../php/audit.php');

    $stmt = $link->prepare('SELECT * FROM users');
    $stmt->execute();
    $resultusers = $stmt->get_result();
    while ($user = $resultusers->fetch_assoc()) {
        if(!is_null($user['discordoauthrefresh']) && !is_null($user['discordrefresh']) && (new DateTime() > new DateTime($user['discordrefresh']))){
            $curl = curl_init();
            $post = [
                'client_id' => OAUTH2_CLIENT_ID,
                'client_secret' => OAUTH2_CLIENT_SECRET,
                'grant_type'   => 'refresh_token',
                'refresh_token' => $user['discordoauthrefresh'],
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

            if(isset($jResponse->error)){
                $null = NULL;
                $stmt = $link->prepare("UPDATE users SET discordid=? WHERE id=?");
                $stmt->bind_param("ss", $null, $user['id']);
                $stmt->execute();
                $stmt->close();
                $stmt = $link->prepare("UPDATE users SET discordoauth=? WHERE id=?");
                $stmt->bind_param("ss", $null, $user['id']);
                $stmt->execute();
                $stmt->close();
                $stmt = $link->prepare("UPDATE users SET discordoauthrefresh=? WHERE id=?");
                $stmt->bind_param("ss", $null, $user['id']);
                $stmt->execute();
                $stmt->close();
                $stmt = $link->prepare("UPDATE users SET discordrefresh=? WHERE id=?");
                $stmt->bind_param("ss", $null, $user['id']);
                $stmt->execute();
                $stmt->close();
                discordAudit("Refresh Token(1) Caught Error", $user['id'], $response);
            }else{
                $curl2 = curl_init();
                curl_setopt_array($curl2, [
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
                $response2 = curl_exec($curl2);
                curl_close($curl2);
                $jResponse2 = json_decode($response2);
                discordAudit("@me(2)", $user['id'], $response2);
                $payload = json_encode( array( "access_token" => $jResponse->access_token ) );
                if(!(bool)$user['discordbanned']){
                    $curl3 = curl_init();
                    curl_setopt_array($curl3, [
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
                    curl_exec($curl3);
                    curl_close($curl3);
                }
                discordAudit("Update(3)", $user['id'], $jResponse->access_token);
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
            }
        }
    }
    

?>