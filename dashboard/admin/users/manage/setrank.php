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

    if(!isset($_GET['rank']) &&!isset($_GET['id'])){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberofrows = $stmt->num_rows;
    if($numberofrows < 1){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $selecteduser = $result->fetch_assoc();

    switch($_GET['rank']){
        case "tester":
            $rankValue = !(bool)$selecteduser['tester'];
            $stmt = $link->prepare("UPDATE users SET tester=? WHERE id=?");
            $stmt->bind_param("ss", $rankValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
        case "admin":
            $rankValue = !(bool)$selecteduser['admin'];
            $stmt = $link->prepare("UPDATE users SET admin=? WHERE id=?");
            $stmt->bind_param("ss", $rankValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
        case "superadmin":
            $rankValue = !(bool)$selecteduser['superadmin'];
            $stmt = $link->prepare("UPDATE users SET superadmin=? WHERE id=?");
            $stmt->bind_param("ss", $rankValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
    }
    

    header("location: ./?id=".$_GET['id']);
    exit;

?>