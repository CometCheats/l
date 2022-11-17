<?php

function isLoggedIn(){
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
                return true;
            }
        }
    }
    
    return false;
}

function hwid($hwid, $id){
    global $link;
    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if(is_null($user['hwid'])){
        $stmt = $link->prepare("UPDATE users SET hwid=? WHERE id=?");
        $stmt->bind_param("ss", $hwid, $id);
        $stmt->execute();
        $stmt->close();
        return true;
    }else{
        return $hwid == $user['hwid'];
    }
}

?>