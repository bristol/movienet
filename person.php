<?php

    require("config.php");

    $db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    if ($db->connect_errno) {
        echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        exit(1);
    }

    if (array_key_exists("pid", $_GET)) {
        $pid = $_GET["pid"];


    } else {
        echo "<div class='mfsearchbar'>Search bar</div>";
    }

    /*

    $statement = "SELECT * FROM People WHERE pid=$pid";

    $res = $db->query($statement);

    foreach ($res as $person) {
        echo $person;
    }

    */
?>
