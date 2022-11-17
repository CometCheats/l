<?php

    require('../../../../php/db.php');
    require('../../../../php/tools.php');
    requireLogin();
    requireAdmin();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(!isset($_POST['id'])){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
    $stmt->bind_param('s', $_POST['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $numberofrows = $stmt->num_rows;
    if($numberofrows < 1){
        header("location: ../");
        exit;
    }
    $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
    $stmt->bind_param('s', $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $download = $result->fetch_assoc();

    $path = "/home/resonance/public_html/internal_files/".$download['filename'];

    unlink($path);
    move_uploaded_file($_FILES['file']['tmp_name'], $path);
    
    $checksum = md5_file($path);
    $stmt = $link->prepare("UPDATE downloads SET checksum=? WHERE id=?");
    $stmt->bind_param("ss", $checksum, $_POST['id']);
    $stmt->execute();
    $stmt->close();

    header("location: ./?id=".$_POST['id']);
    exit;

?>