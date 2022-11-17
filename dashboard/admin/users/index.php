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

    $stmt = $link->prepare('SELECT * FROM users');
    $stmt->execute();
    $resultusers = $stmt->get_result();

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" type="text/css" href="../../../css/style.css">
        <link rel="icon" href="../../../css/favicon.png" sizes="any">
        <title>Users | Resonance Cheats</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div style="width: 99%;margin-left: auto;margin-right: auto;border: 1px solid white;">
            <table id="usersTable" class="table table-bordered table-dark" style="width: 100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>License Key</th>
                        <th>Discord</th>
                        <th>Discord Banned</th>
                        <th>Last HWID Reset</th>
                        <th>Last Login</th>
                        <th>Creation</th>
                        <th>Banned</th>
                        <th>Manage</th>
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
            <a href="../../../privacy/?ref=dashboard/admin/users">
                <span>Privacy Policy</span> 
            </a>         
            <span> | </span>
            <a href="../../../tos/?ref=dashboard/admin/users">
                <span>Terms of Service</span> 
            </a>   
            <span> | </span>      
        </footer>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready( function () {
            $('#usersTable').DataTable({
                "serverSide": true,
                "processing": true,
                "select": true,
                "scrollX": true,
                "pageLength": 10,
                "ajax": {
                    "url": "../../../php/tables.php",
                    "data": function(data) {
                        data.source = "users";
                    }
                }
            });
        } );
    </script>
</html>