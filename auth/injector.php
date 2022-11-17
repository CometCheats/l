<?php
    require('../php/db.php'); 
    require('../php/user.php');
    require('../php/audit.php');
    require('../php/tools.php'); 

    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['version'])){
        $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows < 1){
            audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", NULL, "USER_NOT_FOUND"); 
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
            audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", $userid, "USER_BANNED"); 
            echo json_encode(array("success" => false, "error" => "Resonance user is banned"));
            exit;
        }

        if(password_verify($_POST['password'], $row['password'])){
            $key = "resonance";
            $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();
            $info = $result->fetch_assoc();
            if(!(bool)$info['regularAuth'] && !(bool)$row['admin']){
                if(!(bool)$info['testerAuth'] || !(bool)$row['tester']){
                    audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", $userid, "AUTH_OFFLINE"); 
                    echo json_encode(array("success" => false, "error" => "Resonance Auth is Offline"));
                    exit;
                }
            }
            if(!(bool)$row['tester'] && $_POST['version'] !== $info['regularInjectorVersion']){
                audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", $userid, "VERSION_MISMATCH"); 
                echo json_encode(array("success" => false, "error" => "Version mismatch"));
                exit;
            }
            if((bool)$row['tester'] && $_POST['version'] !== $info['testerInjectorVersion'] && $_POST['version'] !== $info['regularInjectorVersion']){
                audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", $userid, "VERSION_MISMATCH"); 
                echo json_encode(array("success" => false, "error" => "Version mismatch"));
                exit;
            }
            audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", $userid, "SUCCESSFUL_LOGIN"); 
            echo json_encode(array("success" => true));
            exit;
        }else{
            audit("CLIENT_INJECTOR_LOGIN (".$_POST['username'].")", $userid, "INVALID_PASSWORD"); 
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