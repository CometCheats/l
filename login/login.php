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
    
    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['h-captcha-response'])){ // Verify form data has been sent
        $data = array(
            'secret' => "0x8FDFb24994B7D98A5Ee682379792CDc972c19c87",
            'response' => $_POST['h-captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $responseData = json_decode($response);
        if($responseData->success) {
            $stmt = $link->prepare('SELECT * FROM users WHERE username=? OR email=?');
            $stmt->bind_param('ss', $_POST['username'], $_POST['username']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberofrows = $stmt->num_rows;
            if($numberofrows < 1){
                $_SESSION['loginfailedreason'] = "Username/Email not found"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
                audit("CLIENT_WEB_LOGIN (".$_POST['username'].")", NULL, "USER_NOT_FOUND"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
            $stmt = $link->prepare('SELECT * FROM users WHERE username=? OR email=?');
            $stmt->bind_param('ss', $_POST['username'], $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $userid = $row['id'];

            if((bool)$row['banned']){ // Checks if the user has been banned so we can prevent them from further logging in
                $_SESSION['loginfailedreason'] = "Resonance Account Banned"; // Set a session veriable so we can let them know they are banned
                audit("CLIENT_WEB_LOGIN (".$_POST['username'].")", $userid, "USER_BANNED"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }

            if(password_verify($_POST['password'], $row['password'])){
                audit("CLIENT_WEB_LOGIN (".$_POST['username'].")", $userid, "SUCCESSFUL_LOGIN"); // Log the login
                lastlogin($userid);
                $_SESSION['loggedin'] = true;
                $_SESSION['userid'] = $userid;
                header("location: ../dashboard/");
                exit;
            }else{
                $_SESSION['loginfailedreason'] = "Invalid Password"; // Set a session veriable so we can let them know they are banned
                audit("CLIENT_WEB_LOGIN (".$_POST['username'].")", $userid, "INVALID_PASSWORD"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
        }else{
            $_SESSION['loginfailedreason'] = "Invalid Captcha"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
            audit("CLIENT_WEB_LOGIN (".$_POST['username'].")", NULL, "USER_INVALID_CAPTCHA"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }
    }else{
        audit("CLIENT_WEB_LOGIN", NULL, "INSUFFICIENT_INPUT"); // Log the login
        header("location: ./");
        exit;
    }

?>