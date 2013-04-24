<?php require_once("config.php"); ?>
<?php include_once("cookie.php"); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Movies on Movienet</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>

<?php

    //connect to the db
    $db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    if ($db->connect_errno) {
        echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        exit(1);
    }

    if (array_key_exists("m", $_GET)) {
        //display information about a specific movie

        $mid = $_GET["m"];

        $statement = "select * from movie where mid=$mid";
        $response = $db->query($statement);

        $response->data_seek(0);
        while ($row = $response->fetch_assoc()) {
            echo $row;
        }
        

    } else {
        //search functionality

	    $searchfield = "<div class='hero-unit'>\n";
            $searchfield .= "<p>Search for movies on Movienet</p>\n";
            $searchfield .= "<form class='form-search'>\n";
            $searchfield .= "<input type='text' name='q' class='input-xxlarge' placeholder='Name of movie'>\n";
            $searchfield .= "<button type='submit' class='btn btn-primary'>Search</button>\n";
            $searchfield .= "</form>\n";
            $searchfield .= "</div>\n";
            echo $searchfield;

        if (array_key_exists("q", $_GET)) {
            $statement = "select * from movies where title like '%" . $_GET["q"] . "%';";
		$response = $db->query($statement);
		$response->data_seek(0);

		$search = "<div class='search-results'> \n";
		$search .= "<h2>Search results for " . $_GET["q"] . "</h2> \n";
		$search .= "<table class='table table-striped table-hover'> \n";
		$search .= "<thead> <tr> <th>Title</th> <th>Year</th> <th>Running time</th> </tr> </thead> \n";
		$search .= "<tbody> \n";

		while ($row = $response->fetch_assoc()) {
			
		}
        }
}

	?>

    </body>
</html>
