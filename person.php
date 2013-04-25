<?php require_once("config.php"); ?>
<?php include_once("cookie.php"); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Actors, Directors, and Producers on Movienet</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>

	<div class='container'>

<?php

    //connect to the db
    $db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    if ($db->connect_errno) {
        echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        exit(1);
    }

    if (array_key_exists("pid", $_GET)) {
        //display information about a specific person

        $pid = $_GET["pid"];

        $statement = "select * from people where pid=$pid";
        $response = $db->query($statement);

        $response->data_seek(0);
        while ($row = $response->fetch_assoc()) {
            echo $row;
        }
        

    } else {
        //search functionality

        if (array_key_exists("q", $_GET)) {
            //search results
        } else {
            //create the search bar
            $searchfield = "<div class='hero-unit'>\n";
            $searchfield .= "<p>Search for artists on Movienet</p>\n";
            $searchfield .= "<form class='form-search'>\n";
            $searchfield .= "<input type='text' name='q' class='input-xxlarge' placeholder='Name of actor, director, or producer'>\n";
            $searchfield .= "<button type='submit' class='btn btn-primary'>Search</button>\n";
            $searchfield .= "</form>\n";
            $searchfield .= "</div>\n";
            echo $searchfield;
        }
    }
?>

	</div>

    </body>
</html>
