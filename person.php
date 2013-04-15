<?php require_once("config.php"); ?>

<html>
    <head>
        <title>Actors, Directors, and Producers on Movienet</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>

<?php

    $db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    if ($db->connect_errno) {
        echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        exit(1);
    }

    if (array_key_exists("pid", $_GET)) {
        $pid = $_GET["pid"];


    } else {
        $searchfield = "<div class='hero-unit'>\n";
        $searchfield .= "<p>Search for actors, directors, and producers</p>\n";
        $searchfield .= "<form class='form-search'>\n";
        $searchfield .= "<input type='text' name='q' class='input-xxlarge' placeholder='Name of actor, director, or producer'>\n";
        $searchfield .= "<button type='submit' class='btn btn-primary'>Search</button>\n";
        $searchfield .= "</form>\n";
        $searchfield .= "</div>\n";
        echo $searchfield;
    }

    /*

    $statement = "SELECT * FROM People WHERE pid=$pid";

    $res = $db->query($statement);

    foreach ($res as $person) {
        echo $person;
    }

    */
?>

    </body>
</html>
