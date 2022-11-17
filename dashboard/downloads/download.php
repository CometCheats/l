<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(isset($_GET['id'])){
        $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
        $stmt->bind_param('s', $_GET['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberOfUsers = $stmt->num_rows;
        if($numberOfUsers > 0){
            $stmt = $link->prepare('SELECT * FROM downloads WHERE id=?');
            $stmt->bind_param('s', $_GET['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $download = $result->fetch_assoc();
            if((bool)$download['public'] && (bool)$download['enabled']){
                $name = "../../internal_files/".$download['filename'];
                header("Content-Type: file");
                header("Content-Length: " . filesize($download['name']));
                header('Content-Disposition: attachment; filename='.$download['frontFilename']);
                ob_clean();
                flush();
                readfile($name);
                exit;
            }else{
                header("location: ./");
                exit;
            }
        }else{
            header("location: ./");
            exit;
        }
    }else{
        header("location: ./");
        exit;
    }

?>