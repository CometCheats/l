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
    
    if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['license']) && isset($_POST['password']) && isset($_POST['h-captcha-response'])){ // Verify form data has been sent
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
            $stmt = $link->prepare('SELECT * FROM users WHERE username=? OR email=? OR license=?');
            $stmt->bind_param('sss', $_POST['username'], $_POST['email'], $_POST['license']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberofrows = $stmt->num_rows;
            if($numberofrows > 0){
                $_SESSION['registerfailedreason'] = "Username, email, or license already used"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
                audit("CLIENT_WEB_REGISTER (".$_POST['username'].")", NULL, "USER_DUPLICATE"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
            $stmt = $link->prepare('SELECT * FROM licensekeys WHERE license=?');
            $stmt->bind_param('s', $_POST['license']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $numberofrows = $stmt->num_rows;
            if($numberofrows < 1){
                $_SESSION['registerfailedreason'] = "License not found"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
                audit("CLIENT_WEB_REGISTER (".$_POST['username'].")", NULL, "USER_LICENSE_NOT_FOUND"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
            $stmt = $link->prepare('SELECT * FROM licensekeys WHERE license=?');
            $stmt->bind_param('s', $_POST['license']);
            $stmt->execute();
            $result = $stmt->get_result();
            $license = $result->fetch_assoc();
            if((bool)$license["redeemed"]){
                $_SESSION['registerfailedreason'] = "License redeemed"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
                audit("CLIENT_WEB_REGISTER (".$_POST['username'].")", NULL, "USER_LICENSE_REDEEMED"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
            if (preg_match('/\s/',$_POST['username'])){
                $_SESSION['registerfailedreason'] = "Invalid Username (No Spaces)"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
                audit("CLIENT_WEB_REGISTER (".$_POST['username'].")", NULL, "USER_INVALID_USERNAME"); // Log the login
                header("location: ./"); // Redirect back to login screen
                exit;
            }
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $userid = uuid();
            $stmt = $link->prepare("INSERT INTO users_confirmation (username, email, password, license, id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $_POST['username'], $_POST['email'], $password, $_POST['license'], $userid);
            $stmt->execute();
            $stmt->close();

            $code = randomString(64);
            $id = uuid();
            $stmt = $link->prepare("INSERT INTO confirmations (code, user, type, id) VALUES (?, ?, 'USER_REGISTER', ?)");
            $stmt->bind_param("sss", $code, $userid, $id);
            $stmt->execute();
            $stmt->close();
            $headers = array( 
                'From' => "<resonancecheats@gmail.com>",
                'To' => $_POST['email'],
                'Subject' => "Confirm your Resonance Cheats account",
                'MIME-Version' => 1,
                'Content-type' => 'text/html;charset=iso-8859-1'
            );          
            $message = "Thank you for creating your Resonance account.<br> In order to finalize your account you must confirm your email using the link below<br><a href=\"https://resonancecheats.com/register/confirmation.php?code=".$code."\">Confirm now</a><br><br>Thank you,<br>~ Resonance Management";
            $smtp = Mail::factory('smtp', array(
                'host' => 'ssl://smtp.gmail.com',
                'port' => '465',
                'auth' => true,
                'username' => 'resonancecheats@gmail.com',
                'password' => 'skctnfghlbssaadc'
            ));
            $mail = $smtp->send($_POST['email'], $headers, $message);
            $_SESSION['registerfailedreason'] = "A confirmation email has been sent to you. Check your spam folder if you cannot find it, otherwise contact support."; // Set a session veriable giving a reason for redirect if we can't find the details they provided
            audit("CLIENT_WEB_REGISTER (".$_POST['username'].")", NULL, "USER_AWAITING_CONFIRMATION"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }else{
            $_SESSION['registerfailedreason'] = "Invalid Captcha"; // Set a session veriable giving a reason for redirect if we can't find the details they provided
            audit("CLIENT_WEB_REGISTER (".$_POST['username'].")", NULL, "USER_INVALID_CAPTCHA"); // Log the login
            header("location: ./"); // Redirect back to login screen
            exit;
        }
    }else{
        audit("CLIENT_WEB_REGISTER", NULL, "INSUFFICIENT_INPUT"); // Log the login
        header("location: ./");
        exit;
    }

?>