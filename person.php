<?php

    require("config.php");

    $mysqli = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);

    $pid = $_GET["pid"];

    $statement = "SELECT * FROM People WHERE pid=$pid"

    if ($db->connect_errno) {
        echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        exit(1);
    }

    $res = $db->query($statement);

    foreach ($res as $person) {
        echo $person;
    }
?>
