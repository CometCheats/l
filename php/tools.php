<?php
session_start();

function uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function randomName($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function requireLogin() {
    global $link;
    if(isset($_SESSION['loggedin'])){
        $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
        $stmt->bind_param('s', $_SESSION['userid']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows > 0){
            $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
            $stmt->bind_param('s', $_SESSION['userid']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if(!(bool)$user["banned"]){
                return;
            }
        }
    }
    session_destroy();
    header("location: http://".$_SERVER['HTTP_HOST']."/login/");
    exit;
}

function requireAdmin() {
    global $link;
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if(!(bool)$user["admin"]){
        header("location: http://".$_SERVER['HTTP_HOST']."/dashboard/");
        exit;
    }
}

?>