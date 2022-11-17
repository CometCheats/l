<?php

    require('../../../../php/db.php');
    require('../../../../php/tools.php');
    requireLogin();
    requireAdmin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(isset($_GET['id'])){
        $key = "resonance";
        $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        $info = $result->fetch_assoc();
        $stmt = $link->prepare('SELECT * FROM sessions WHERE id=?');
        $stmt->bind_param('s', $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $sessions = $result->fetch_assoc();
        $timeUsed = strtotime($sessions['expiration']) - strtotime($sessions['created']);
        $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
        $stmt->bind_param('s', $sessions['user']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $newUserTime = ($user['time'] + $timeUsed)-25;
        $newGlobalTime = ($info['time'] + $timeUsed)-25;
        $stmt = $link->prepare("UPDATE users SET time=? WHERE id=?");
        $stmt->bind_param("ss", $newUserTime, $user['id']);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("UPDATE information SET time=? WHERE id=?");
        $stmt->bind_param("ss", $newGlobalTime, $key);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("INSERT INTO old_sessions SELECT * FROM sessions WHERE id=?");
        $stmt->bind_param("s", $sessions['id']);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("DELETE FROM sessions WHERE id=?");
        $stmt->bind_param("s", $sessions['id']);
        $stmt->execute();
        $stmt->close();
        header("location: ../");
        exit;
    }else{
        header("location: ../");
        exit;
    }

?>