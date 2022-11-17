<?php

    require('../../../php/db.php');
    require('../../../php/tools.php');
    requireLogin();
    requireAdmin();

    $stmt = $link->prepare('SELECT * FROM users WHERE id=?');
    $stmt->bind_param('s', $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>Sessions | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div style="width: 99%;margin-left: auto;margin-right: auto;border: 1px solid white;">
            <table id="sessionsTable" class="table table-bordered table-dark" style="width: 100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>IP</th>
                        <th>Game Name</th>
                        <th>Started</th>
                        <th>Time Active</th>
                        <th>Delete</th>
                    </tr>
                </thead>
            </table>
        </div>
        <br><br><br>
        <footer>
            <span> | </span>
            <a href="../">
                <span>Back</span> 
            </a>         
            <span> | </span>
            <a href="../../../privacy/?ref=dashboard/admin/sessions">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../../tos/?ref=dashboard/admin/sessions">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function loadTable(){
            window.location.reload();
            setTimeout(loadTable, 5000);
        }
        $(document).ready( function () {
            $('#sessionsTable').DataTable({
                "serverSide": true,
                "processing": true,
                "select": true,
                "scrollX": true,
                "pageLength": 10,
                "ajax": {
                    "url": "../../../php/tables.php",
                    "data": function(data) {
                        data.source = "sessions";
                    }
                }
            });
            setTimeout(loadTable, 5000);
        } );
    </script>
</html>