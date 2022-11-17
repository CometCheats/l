<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    require('../../php/audit.php');

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://discord.com/api/v9/guilds/945678632523825212/members?limit=1000",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_HTTPHEADER => [
            "Content-Type: multipart/form-data;boundary=",
            "Authorization: Bot ".DISCORD_BOT_TOKEN
        ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    $jResponse = json_decode($response);

    foreach ($jResponse as &$user) {
        $id = $user->user->id;
        if($id == 945808242917904414 || $id == 926991373624827905 || $id == 871546917459468290){
            continue;
        }
        $stmt = $link->prepare('SELECT * FROM users WHERE discordid=? AND discordbanned="0" AND banned="0"');
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows == 0){
            $payload = json_encode( array( "recipient_id" => $id ) );
            $curl2 = curl_init();
            curl_setopt_array($curl2, [
                CURLOPT_URL => "https://discord.com/api/v9/users/@me/channels",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bot ".DISCORD_BOT_TOKEN
                ],
            ]);
            $response2 = curl_exec($curl2);
            curl_close($curl2);
            $jResponse2 = json_decode($response2);
            $channelID = $jResponse2->id;

            $payload = json_encode( array( "content" => "You have been removed from the Resonance Verified Discord Server due to your account no longer be linked, your Resonance account being banned, or you being Discord banned. To rejoin the server, link your account on the website. If you need help, join the buyers server.\n\n**Resonance Website:** https://resonancecheats.com/\n**Buyers Discord:** https://discord.gg/YpVEjvy77D\n\nP.S we are currently developing our Discord system, and it has a few bugs, if you did not unlink your account, and this was done randomly, please DM Vex once you have relinked your account." ) );
            $curl3 = curl_init();
            curl_setopt_array($curl3, [
                CURLOPT_URL => "https://discord.com/api/v9/channels/".$channelID."/messages",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bot ".DISCORD_BOT_TOKEN
                ],
            ]);
            $response3 = curl_exec($curl3);
            curl_close($curl3);
            $jResponse3 = json_decode($response3);
            $curl4 = curl_init();
            curl_setopt_array($curl4, [
                CURLOPT_URL => "https://discord.com/api/v9/guilds/945678632523825212/members/".$id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bot ".DISCORD_BOT_TOKEN
                ],
            ]);
            curl_exec($curl4);
            curl_close($curl4);
            discordAudit("Remove from discord server", $id, "Removed");
        }  
    }
?>