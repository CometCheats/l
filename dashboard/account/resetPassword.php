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

    if(isset($_GET['new']) && isset($_GET['old'])){
        if(password_verify($_GET['old'], $user['password'])){
            $new = password_hash($_GET['new'], PASSWORD_DEFAULT);
            $stmt = $link->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("ss", $new, $user['id']);
            $stmt->execute();
            $stmt->close();
            $stmt = $link->prepare("UPDATE users SET passwordreset=CURRENT_TIMESTAMP WHERE id=?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();
            $stmt->close();
            session_destroy();
            audit("CLIENT_WEB_RESET_PASSWORD", $user["id"], "SUCCESSFUL_RESET");
            header("location: ./");
        }else{
            audit("CLIENT_WEB_RESET_PASSWORD", $user["id"], "CURRENT_PASSWORD_INVALID");
            header("location: ./?err=oldinvalid");
            exit;
        }
    }else{
        audit("CLIENT_WEB_RESET_PASSWORD", $user["id"], "INSUFFICIENT_INPUT");
        header("location: ./");
        exit;
    }


?>