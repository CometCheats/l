<?php

    require('../../php/db.php'); // MySQL connection
    require('../../php/user.php'); // Various user account functions
    require('../../php/audit.php'); // Auditing
    require('../../php/tools.php'); //General tools
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(!is_null($user['hwid'])){ 
        if(!is_null($user['hwidreset'])){ 
            if(strtotime(date('Y-m-d H:i:s')) > strtotime("+7 day", strtotime($user['hwidreset']))){ 
                $stmt = $link->prepare("UPDATE users SET hwid=NULL WHERE id=?");
                $stmt->bind_param("s", $user['id']);
                $stmt->execute();
                $stmt->close();
                $stmt = $link->prepare("UPDATE users SET hwidreset=CURRENT_TIMESTAMP WHERE id=?");
                $stmt->bind_param("s", $user['id']);
                $stmt->execute();
                $stmt->close();
                audit("CLIENT_WEB_RESET_HWID", $user["id"], "SUCCESSFUL_RESET");
                header("location: ./");
                exit;
            }else{ 
                audit("CLIENT_WEB_RESET_HWID", $user["id"], "NO_RESET_AVAILABLE");
                header("location: ./");
                exit;
            } 
        }else{ 
            $stmt = $link->prepare("UPDATE users SET hwid=NULL WHERE id=?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();
            $stmt->close();
            $stmt = $link->prepare("UPDATE users SET hwidreset=CURRENT_TIMESTAMP WHERE id=?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();
            $stmt->close();
            audit("CLIENT_WEB_RESET_HWID", $user["id"], "SUCCESSFUL_RESET");
            header("location: ./");
            exit;
        } 
    }else{ 
        audit("CLIENT_WEB_RESET_HWID", $user["id"], "CURRENT_HWID_BLANK");
        header("location: ./");
        exit;
    } 

?>