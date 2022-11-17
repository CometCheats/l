<?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'resonance');
    define('DB_PASSWORD', 'n3hehb8b4qF2YMCDrWJwS7UGHuYZAjbDbSfbafArNB8z8ndctANhufhqQgtek6JTBkGVLDRp2VzSdRxttYkYXhMMWu6MaJWwThRzQkrnNPptE5bv34fBsRcLvmXZChfq?');
    define('DB_NAME', 'resonance_main');
    define('OAUTH2_CLIENT_ID', '945808242917904414');
    define('OAUTH2_CLIENT_SECRET', '1VNLo_PVjq5MCR8tAmL-K57gqs5ckqXL');
    define('DISCORD_ID', '945678632523825212');
    define('DISCORD_BOT_TOKEN', 'OTQ1ODA4MjQyOTE3OTA0NDE0.YhVikQ.FcNPGzgP7tddPpW7U5R5uBw_mn8');

    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if($link === false){
        die("Resonance ran into an error " . mysqli_connect_error());
    }
?>

