<?php
    require('../php/db.php'); 
    require('../php/user.php');
    require('../php/audit.php');
    require('../php/tools.php'); 

    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['hwid']) && isset($_POST['scid']) && isset($_POST['version'])){
        $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows < 1){
            audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", NULL, "USER_NOT_FOUND"); 
            echo json_encode(array("success" => false, "error" => "Resonance user not found"));
            exit;
        }
        $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $userid = $row['id'];
        $userauthentications = $row['authentications'];

        if((bool)$row['banned']){ 
            audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "USER_BANNED"); 
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
                        audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "AUTH_OFFLINE"); 
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
                audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "SUCCESSFUL_LOGIN"); 
                $newAuthentications = $info['authentications']+1;
                $stmt = $link->prepare("UPDATE information SET authentications=? WHERE id=?");
                $stmt->bind_param("ss", $newAuthentications, $key);
                $stmt->execute();
                $stmt->close();
                $newUserAuthentications = $userauthentications+1;
                $stmt = $link->prepare("UPDATE users SET authentications=? WHERE id=?");
                $stmt->bind_param("ss", $newUserAuthentications, $userid);
                $stmt->execute();
                $stmt->close();
                $scuuid = uuid();
                $stmt = $link->prepare("INSERT INTO scids (scid, user, id) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $_POST['scid'], $userid, $scuuid);
                $stmt->execute();
                $stmt->close();
                echo json_encode(array("success" => true));
                exit;
            }else{
                audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "INVALID_HWID"); 
                echo json_encode(array("success" => false, "error" => "Invalid HWID"));
                exit;
            }
        }else{
            audit("CLIENT_MENU_LOGIN (".$_POST['username'].")", $userid, "INVALID_PASSWORD"); 
            echo json_encode(array("success" => false, "error" => "Invalid Password"));
            exit;
        }
    }else{
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

?>