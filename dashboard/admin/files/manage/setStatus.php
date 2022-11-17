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

    if(!isset($_GET['type']) &&!isset($_GET['id'])){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberofrows = $stmt->num_rows;
    if($numberofrows < 1){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $download = $result->fetch_assoc();

    switch($_GET['type']){
        case "enabled":
            $typeValue = !(bool)$download['enabled'];
            $stmt = $link->prepare("UPDATE downloads SET enabled=? WHERE id=?");
            $stmt->bind_param("ss", $typeValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
        case "public":
            $typeValue = !(bool)$download['public'];
            $stmt = $link->prepare("UPDATE downloads SET public=? WHERE id=?");
            $stmt->bind_param("ss", $typeValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
        case "injector":
            $typeValue = !(bool)$download['injector'];
            $stmt = $link->prepare("UPDATE downloads SET injector=? WHERE id=?");
            $stmt->bind_param("ss", $typeValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
        case "noEdit":
            $typeValue = !(bool)$download['noEdit'];
            $stmt = $link->prepare("UPDATE downloads SET noEdit=? WHERE id=?");
            $stmt->bind_param("ss", $typeValue, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        break;
    }
    
    header("location: ./?id=".$_GET['id']);
    exit;

?>