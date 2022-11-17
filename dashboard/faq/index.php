<?php

    require('../../php/db.php');
    require('../../php/tools.php');
    requireLogin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <script src="../../../js/dashboard.js"></script>
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>FAQ | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="dashboard-header">
            <h1>FAQ</h1>
            <h4>Frequently Asked Question</h4>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>How do I get started?</h2>
            <h3>Go to your Dashboard->Downloads->Download Launcher and run the exe. It will automatically install all the files needed to use Resonance. You may need to disable anti-virus, and/or make an exception rule for Resonance.</h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>How do I use Downloadable Resonance Content?</h2>
            <h3>You can download additional content from the Downloads section of the website, or in our Discord. To use it put it in it's correct folder in the Resonance directory. This directory can be found at C:\Users\{Your User}\Documents\Resonance\.</h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Can I use Resonance on more than 1 computer?</h2>
            <h3>Yes and no. Every 7 days you can reset your HWID, which will let you use the menu on a different computer. Note, account sharing is not allowed, and will result in account termination. This system should only be used to use Resonance on your own PC(s).</h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Can I sell my Resonance account?</h2>
            <h3>No. We monitor all account activity, and if we notice activity similar to that of a sold account, you may be investigated, and if found that you were account was sold, it will be terminated.</h3>
        </div>
        <div class="divider"></div>
        <div class="dashboard-header">
            <h2>Will Resonance shut down?</h2>
            <h3>We have no plans of shutting down anytime soon. However with that being said, this project will not last forever, and it's important to remeber that when you purchase a lifetime license your buying a license for the lifetime of Resonance, not your lifetime. Many different factors may cause a shutdown, such as legal problems, personal safety issues, or our personal lives just in general.</h3>
        </div>
        <div class="divider"></div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../privacy/?ref=dashboard/faq">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../tos/?ref=dashboard/faq">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>