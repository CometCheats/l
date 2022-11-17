<?php
    require('../php/db.php'); 
    require('../php/user.php');
    require('../php/audit.php');
    require('../php/tools.php'); 

    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['program'])){
        $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows < 1){
            audit("CLIENT_INJECTOR_DEBUGGING (".$_POST['username'].")", NULL, "USER_NOT_FOUND"); 
            exit;
        }
        $stmt = $link->prepare('SELECT * FROM users WHERE username=?');
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $userid = $row['id'];

        if((bool)$row['banned']){ 
            audit("CLIENT_INJECTOR_DEBUGGING (".$_POST['username'].")", $userid, "USER_BANNED"); 
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit;
        }

        if(password_verify($_POST['password'], $row['password'])){
            $key = "resonance";
            $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();
            $info = $result->fetch_assoc();
            $stmt = $link->prepare('UPDATE users SET BANNED="1" WHERE id=?');
            $stmt->bind_param("s", $userid);
            $stmt->execute();
            $stmt->close();
            audit("CLIENT_INJECTOR_DEBUGGING (".$_POST['username'].") (".$_POST['program'].")", $userid, "USER_BANNED"); 
        }else{
            audit("CLIENT_INJECTOR_DEBUGGING (".$_POST['username'].")", $userid, "INVALID_PASSWORD"); 
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit;
        }
    }else{
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

?>