<?php
    require('../php/db.php'); 
    require('../php/user.php');
    require('../php/audit.php');
    require('../php/tools.php'); 

    $key = "resonance";
    $stmt = $link->prepare('SELECT * FROM information WHERE id=?');
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();

    $stmt = $link->prepare('SELECT * FROM directories');
    $stmt->execute();
    $directoriesResults = $stmt->get_result();

    $stmt = $link->prepare('SELECT * FROM downloads WHERE injector="1" ORDER BY priority');
    $stmt->execute();
    $downloadsResults = $stmt->get_result();

    $manifest = array(
        "version" => $info['testerInjectorVersion'],
        "filesystem" => []
    );

    while($directoriesRow = $directoriesResults->fetch_assoc()){
        array_push($manifest['filesystem'], array(
            "type" => "directory",
            "path" => $directoriesRow['path']
        ));
    }

    while($downloadsRow = $downloadsResults->fetch_assoc()){
        array_push($manifest['filesystem'], array(
            "type" => "file",
            "source" => $downloadsRow['id'],
            "path" => $downloadsRow['path'],
            "noEdit" => (bool)$downloadsRow['noEdit'],
            "checksum" => $downloadsRow['checksum'],
        ));
    }

    echo json_encode($manifest);
?>