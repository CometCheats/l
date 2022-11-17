<?php
    require('../php/db.php'); // MySQL connection
    require('../php/user.php'); // Various user account functions
    require('../php/audit.php'); // Auditing
    require('../php/tools.php'); //General tools
    require('Mail.php');

    if(isLoggedIn()){
        header("location: ../dashboard/"); // Redirect to dashboard if already logged in
        exit;
    }

    $userid = NULL; // To be used once we have verified the account exists
    
    if(isset($_POST['email']) && isset($_POST['h-captcha-response'])){ // Verify form data has been sent
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
            $stmt = $link->prepare('SELECT * FROM users WHERE email=?');
            $stmt->bind_param('s', $_POST['email']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberofrows = $stmt->num_rows;
            if($numberofrows < 1){
                audit("CLIENT_WEB_PASSWORD_RESET (".$_POST['email'].")", NULL, "EMAIL_NOT_FOUND"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
            $stmt = $link->prepare('SELECT * FROM users WHERE email=?');
            $stmt->bind_param('s', $_POST['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            $code = randomString(64);
            $id = uuid();
            $stmt = $link->prepare("INSERT INTO confirmations (code, user, type, id) VALUES (?, ?, 'USER_PASSWORD_RESET', ?)");
            $stmt->bind_param("sss", $code, $user['id'], $id);
            $stmt->execute();
            $stmt->close();
            echo $user['id'];
            $headers = array( 
                'From' => "<resonancecheats@gmail.com>",
                'To' => $_POST['email'],
                'Subject' => "Reset your Resonance Cheats account password",
                'MIME-Version' => 1,
                'Content-type' => 'text/html;charset=iso-8859-1'
            );          
            $message = "You have recently requested a Resonance Account password reset.<br> In order to reset your password click the link below.<br><a href=\"https://resonancecheats.com/login/reset.php?code=".$code."\">Reset now</a><br><br>Thank you,<br>~ Resonance Management";
            $smtp = Mail::factory('smtp', array(
                'host' => 'ssl://smtp.gmail.com',
                'port' => '465',
                'auth' => true,
                'username' => 'resonancecheats@gmail.com',
                'password' => 'skctnfghlbssaadc'
            ));
            $mail = $smtp->send($_POST['email'], $headers, $message);
            audit("CLIENT_WEB_PASSWORD_RESET (".$_POST['username'].")", NULL, "USER_SENT_RESET"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }else{
            audit("CLIENT_WEB_PASSWORD_RESET (".$_POST['email'].")", NULL, "USER_INVALID_CAPTCHA"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }
    }else{
        audit("CLIENT_WEB_PASSWORD_RESET", NULL, "INSUFFICIENT_INPUT"); // Log the login
        header("location: ./");
        exit;
    }

?>