<?php

    require('../php/db.php'); // MySQL connection
    require('../php/user.php'); // Various user account functions
    require('../php/audit.php'); // Auditing
    require('../php/tools.php'); //General tools

    if(isLoggedIn()){
        header("location: ../dashboard/"); // Redirect to dashboard if already logged in
        exit;
    }

    $userid = NULL; // To be used once we have verified the account exists
    
    if(isset($_POST['password']) && isset($_POST['code'])){
        $stmt = $link->prepare('SELECT * FROM confirmations WHERE code=?');
        $stmt->bind_param('s', $_POST['code']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $numberofrows = $stmt->num_rows;
        if($numberofrows < 1){
            $_SESSION['registerfailedreason'] = "Code not found"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
            audit("CLIENT_REGISTER_CONFIRMATION", NULL, "USER_CODE_NOT_FOUND"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }
        $stmt = $link->prepare('SELECT * FROM confirmations WHERE code=?');
        $stmt->bind_param('s', $_POST['code']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $userid = $row['user'];
        $confirmationid = $row['id'];
        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $link->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("ss", $newPassword, $userid);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("DELETE FROM confirmations WHERE id=?");
        $stmt->bind_param("s", $confirmationid);
        $stmt->execute();
        $stmt->close();
        header("location: ./");
        exit;
    }else{
        header("location: ./");
        exit;
    }

?>
