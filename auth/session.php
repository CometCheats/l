<?php
    require('../php/db.php'); 
    require('../php/user.php');
    require('../php/audit.php');
    require('../php/tools.php'); 

    if(isset($_POST['sessionkey']) || ( isset($_POST['username']) && isset($_POST['password'])) && isset($_POST['hwid']) && isset($_POST['gameName']) && isset($_POST['version'])){
        if(isset($_POST['sessionkey'])){
            $stmt = $link->prepare('SELECT * FROM sessions WHERE sessionkey=?');
            $stmt->bind_param('s', $_POST['sessionkey']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberofrows = $stmt->num_rows;
            if($numberofrows < 1){
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", NULL, "Session not found"); 
                echo json_encode(array("success" => false, "error" => "Session has expired/does not exist"));
                exit;
            }
            $stmt = $link->prepare('SELECT * FROM sessions WHERE sessionkey=?');
            $stmt->bind_param('s', $_POST['sessionkey']);
            $stmt->execute();
            $result = $stmt->get_result();
            $session = $result->fetch_assoc();

            $userid = $session['user'];

            $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
            $stmt->bind_param('s', $userid);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if((bool)$user['banned']){ 
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "USER_BANNED"); 
                echo json_encode(array("success" => false, "error" => "Resonance user is banned"));
                exit;
            }
            $key = "resonance";
            $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();
            $info = $result->fetch_assoc();
            if(!(bool)$info['regularAuth'] && !(bool)$user['admin']){
                if(!(bool)$info['testerAuth'] || !(bool)$user['tester']){
                    audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "AUTH_OFFLINE"); 
                    echo json_encode(array("success" => false, "error" => "Resonance Auth is Offline"));
                    exit;
                }
            }
            if(!(bool)$user['tester'] && $_POST['version'] !== $info['regularVersion']){
                audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "VERSION_MISMATCH"); 
                echo json_encode(array("success" => false, "error" => "Version mismatch"));
                exit;
            }
            if((bool)$user['tester'] && $_POST['version'] !== $info['testerVersion'] && $_POST['version'] !== $info['regularVersion']){
                audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "VERSION_MISMATCH"); 
                echo json_encode(array("success" => false, "error" => "Version mismatch"));
                exit;
            }

            if($_POST['gameName'] != $session['gameName']){
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "GAME_NAME_MISMATCH"); 
                echo json_encode(array("success" => false, "error" => "Game name mismatch"));
                exit;
            }

            if(!hwid($_POST['hwid'], $userid)){
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "HWID_MISMATCH"); 
                echo json_encode(array("success" => false, "error" => "HWID mismatch"));
                exit;
            }

            $expiration = date("Y-m-d H:i:s ", time() + 30);

            $stmt = $link->prepare("UPDATE sessions SET expiration=? WHERE id=?");
            $stmt->bind_param("ss", $expiration, $session['id']);
            $stmt->execute();
            $stmt->close();

            echo json_encode(array("success" => true));

        }else{
            $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
            $stmt->bind_param('s', $_POST['username']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberofrows = $stmt->num_rows;
            if($numberofrows < 1){
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", NULL, "USER_NOT_FOUND"); 
                echo json_encode(array("success" => false, "error" => "Resonance user not found"));
                exit;
            }
            $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
            $stmt->bind_param('s', $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $userid = $row['id'];

            if((bool)$row['banned']){ 
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "USER_BANNED"); 
                echo json_encode(array("success" => false, "error" => "Resonance user is banned"));
                exit;
            }
            if(password_verify($_POST['password'], $row['password'])){
                if(hwid($_POST['hwid'], $userid)){
                    $key = "resonance";
                    $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
                    $stmt->bind_param("s", $key);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $info = $result->fetch_assoc();
                    if(!(bool)$info['regularAuth'] && !(bool)$row['admin']){
                        if(!(bool)$info['testerAuth'] || !(bool)$row['tester']){
                            audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "AUTH_OFFLINE"); 
                            echo json_encode(array("success" => false, "error" => "Resonance Auth is Offline"));
                            exit;
                        }
                    }
                    if(!(bool)$row['tester'] && $_POST['version'] !== $info['regularVersion']){
                        audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "VERSION_MISMATCH"); 
                        echo json_encode(array("success" => false, "error" => "Version mismatch"));
                        exit;
                    }
                    if((bool)$row['tester'] && $_POST['version'] !== $info['testerVersion'] && $_POST['version'] !== $info['regularVersion']){
                        audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "VERSION_MISMATCH"); 
                        echo json_encode(array("success" => false, "error" => "Version mismatch"));
                        exit;
                    }
                    $sessionkey = uuid();
                    $ip = getClientIP();
                    $expiration = date("Y-m-d H:i:s ", time() + 30);
                    $id = uuid();
                    $stmt = $link->prepare("INSERT INTO sessions (sessionkey, user, ip, gameName, expiration, id) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $sessionkey, $userid, $ip, $_POST['gameName'], $expiration, $id);
                    $stmt->execute();
                    $stmt->close();
                    echo json_encode(array("success" => true, "sessionKey" => $sessionkey));
                }else{
                    audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "INVALID_HWID"); 
                    echo json_encode(array("success" => false, "error" => "Invalid HWID"));
                    exit;
                }
            }else{
                audit("CLIENT_MENU_SESSION (".$_POST['username'].")", $userid, "INVALID_PASSWORD"); 
                echo json_encode(array("success" => false, "error" => "Invalid Password"));
                exit;
            }
        }
    }else{
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

?>