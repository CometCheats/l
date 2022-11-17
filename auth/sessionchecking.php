<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require('../php/db.php'); 
    require('../php/user.php');
    require('../php/audit.php');
    require('../php/tools.php'); 

    $key = "resonance";
    $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM sessions');
    $stmt->execute();
    $result = $stmt->get_result();
    while ($sessions = $result->fetch_assoc()) {
        if (new DateTime() > new DateTime($sessions['expiration'])) {
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
        }
    }

    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();

?>