<?php require_once("config.php"); ?>
<?php include_once("login.php"); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Movies on Movienet</title>

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

    if (array_key_exists("m", $_GET)) {
        //display information about a specific movie

        $mid = $_GET["m"];

        $statement = "select * from movies where mid=" . $mid . ";";
        $response = $db->query($statement);
	$response->data_seek(0);
	$row = $response->fetch_assoc();
            
	$movie = "<div class='hero-unit'> \n";
	$movie .= "<div class='page-header'> \n";
	$movie .= "<h2>" . $row["title"] . " <small>" . $row["year"] . "</small></h2> \n";
	$movie .= "</div> \n";
	$movie .= "</div> \n";

	echo $movie;

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
			$search .= "<tr> <td><a href='movie.php?m=" . $row["mid"] . "'>" . $row["title"] . "</a></td> <td>" . $row["year"] . "</td> <td>" . $row["runningTime"] . "</td> </tr> \n";	
		}

		$search .= "</tbody> </table> \n";
		
		echo $search;
        }
}

	?>
		</div>

    </body>
</html>
