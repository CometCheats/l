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

    if(!isset($_GET['id'])){
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

    $hwid = NULL;

    $stmt = $link->prepare("UPDATE users SET hwid=? WHERE id=?");
    $stmt->bind_param("ss", $hwid, $_GET['id']);
    $stmt->execute();
    $stmt->close();
    

    header("location: ./?id=".$_GET['id']);
    exit;

?>