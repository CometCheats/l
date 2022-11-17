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
    
    if(isset($_GET['code'])){
        $stmt = $link->prepare('SELECT * FROM confirmations WHERE code=?');
        $stmt->bind_param('s', $_GET['code']);
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
        $stmt->bind_param('s', $_GET['code']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $userid = $row['user'];
        $confirmationid = $row['id'];
        $stmt = $link->prepare("INSERT INTO users SELECT * FROM users_confirmation WHERE id=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("DELETE FROM users_confirmation WHERE id=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("DELETE FROM confirmations WHERE id=?");
        $stmt->bind_param("s", $confirmationid);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
        $stmt->bind_param('s', $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt = $link->prepare("UPDATE licensekeys SET redeemed=1 WHERE license=?");
        $stmt->bind_param("s", $row['license']);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("UPDATE licensekeys SET redeemdate=CURRENT_TIMESTAMP WHERE license=?");
        $stmt->bind_param("s", $row['license']);
        $stmt->execute();
        $stmt->close();
        $stmt = $link->prepare("UPDATE licensekeys SET user=? WHERE license=?");
        $stmt->bind_param("ss", $userid, $row['license']);
        $stmt->execute();
        $stmt->close();
        header("location: ../login/");
        exit;
    }else{
        header("location: ./");
        exit;
    }

?>