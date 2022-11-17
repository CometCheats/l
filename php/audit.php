<?php
    
    function audit($action, $identity, $outcome){
        global $link;
        $ip = getClientIP();
        $id = uuid();
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $stmt = $link->prepare("INSERT INTO auditlog (action, identity, ipaddress, agent, outcome, id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $action, $identity, $ip, $agent, $outcome, $id);
        $stmt->execute();
        $stmt->close();
    }

    function discordAudit($action, $identity, $outcome){
        global $link;
        $id = uuid();
        $stmt = $link->prepare("INSERT INTO discordaudit (action, identity, outcome, id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $action, $identity, $outcome, $id);
        $stmt->execute();
        $stmt->close();
    }

    function lastLogin($id){
        global $link;
        $stmt = $link->prepare("UPDATE users SET lastlogin=CURRENT_TIMESTAMP WHERE id=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->close();
    }
?>