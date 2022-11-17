<?php

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <link rel="stylesheet" href="map.css" type="text/css" media="screen"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="map.js"></script>
        <script src="world.js"></script>
    </head>
    <body>
        <div class="dashboard-header">
            <h1>Welcome, {Username}</h1>
            <h4>Here you can manage your Cobalt Cheats account.</h4>
        </div>
        <div class="divider"></div>
        <div id="world-map" style="width: 600px; height: 400px"></div>
        <script>
            var loginData = {
                "US": 10,
                "GB": 6
            }
            $(function(){
                $('#world-map').vectorMap({
                    map: 'world_mill', 
                    backgroundColor: '#1a1b21',
                    series: {
                        regions: [{
                            values: loginData,
                            scale: ['#C8EEFF', '#0071A4'],
                            normalizeFunction: 'polynomial'
                        }]
                    },
                    onRegionTipShow: function(e, el, code){
                        el.html(el.html()+' (Total Logins - '+loginData[code]+')');
                    }
                });
            });
        </script>
        <div class="divider"></div>
        <footer>
            <span> | </span>
            <a href="../privacy/">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../tos/">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
</html>