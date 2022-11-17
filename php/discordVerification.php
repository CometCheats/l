<?php

    require('db.php');

    $headers = getallheaders();

    if(isset($headers["Token"])){
        if($headers["Token"] == DISCORD_BOT_TOKEN){
            $members = json_decode(file_get_contents('php://input'), TRUE); 
            $final = "[";
            foreach($members as &$member){
                $stmt = $link->prepare('SELECT * FROM users WHERE discordid=?');
                $stmt->bind_param('s', $member);
                $stmt->execute();
                $stmt->store_result();
                $stmt->fetch();
                $numberofrows = $stmt->num_rows;
                if($numberofrows > 0){
                    $stmt = $link->prepare('SELECT * FROM users WHERE discordid=?');
                    $stmt->bind_param('s', $member);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    if(!(bool)$user['discordbanned']){
                        if(next($members)){
                            $final = $final."{\"id\": ".$member.", \"valid\": true},";
                        }else{
                            $final = $final."{\"id\": ".$member.", \"valid\": true}";
                        }
                        continue;
                    }
                }
                if(next($members)){
                    $final = $final."{\"id\": ".$member.", \"valid\": false},";
                }else{
                    $final = $final."{\"id\": ".$member.", \"valid\": false}";
                }
            }
            $final = $final."]";
            header("Content-Type: application/json");
            echo $final;
        }else{
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit();
        }
    }else{
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

?>