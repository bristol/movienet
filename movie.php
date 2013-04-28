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

	if (isset($_COOKIE["username"]) && isset($_GET["rate"])) {
		$statement = "insert into rated (uid, mid, rating) values (" . $_COOKIE["uid"] . ", " . $mid . ", " . $_GET["rate"] . ") on duplicate key update
rating=" . $_GET["rate"] . ";";
		$response = $db->query($statement);
	}

        $statement = "select * from movies where mid=" . $mid . ";";
        $response = $db->query($statement);
	$response->data_seek(0);
	$row = $response->fetch_assoc();

	$movie = array();
	$movie["mid"] = $row["mid"];
	$movie["title"] = $row["title"];
	$movie["year"] = $row["year"];
	$movie["runningTime"] = $row["runningTime"];

	$statement = "select abbreviation from has_mpaa H, mpaaratings R where H.mid=" . $movie["mid"] . " and H.mpaaid=R.mpaaid;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$row = $response->fetch_assoc();

	$movie["mpaarating"] = $row["abbreviation"];

	$statement = "select genre from has_genre H, genres G where H.mid=" . $movie["mid"] . " and H.gid=G.gid;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$genres = array();
	while ($row = $response->fetch_assoc()) {
		array_push($genres, $row["genre"]);
	}
	$movie["genres"] = $genres;

	$statement = "select P.pid, P.name from directed D, people P where D.mid=" . $movie["mid"] . " and D.pid=P.pid;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$directors = array();
	while ($row = $response->fetch_assoc()) {
		array_push($directors, array("name" => $row["name"], "pid" => $row["pid"]));
	}
	$movie["directors"] = $directors;


	/*
	$statement = "select keyword from has_key H, keywords K where H.mid=" . $movie["mid"] . " and H.kid=K.kid;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$keywords = array();
	while ($row = $response->fetch_assoc()) {
		array_push($keywords, $row["keyword"]);
	}
	$movie["keywords"] = $keywords;	
	*/

        $statement = "select P.pid, P.name from produced D, people P where D.mid=" . $movie["mid"] . " and D.pid=P.pid;";
        $response = $db->query($statement);
        $response->data_seek(0);
        $producers = array();
        while ($row = $response->fetch_assoc()) {
                array_push($producers, array("name" => $row["name"], "pid" => $row["pid"]));
        }   
        $movie["producers"] = $producers;
	
	$statement = "select P.pid, P.name from acted_in A, people P where A.mid=" . $movie["mid"] . " and A.pid=P.pid;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$actors = array();
	while ($row = $response->fetch_assoc()) {
		array_push($actors, array("name" => $row["name"], "pid" => $row["pid"], "role" => "role"));
	}
	$movie["actors"] = $actors;

	$statement = "select count(rating), round(avg(rating), 2) from rated where mid=" . $movie["mid"] . ";";
	$response = $db->query($statement);
	$response->data_seek(0);
	$row = $response->fetch_assoc();
	$movie["rating-avg"] = $row["round(avg(rating), 2)"];
	$movie["rating-count"] = $row["count(rating)"];

	if (isset($_COOKIE["username"])) {

		$statement = "select rating from rated where uid=" . $_COOKIE["uid"] . " and mid=" . $movie["mid"] . ";";
		$response = $db->query($statement);
		$response->data_seek(0);
		$row = $response->fetch_assoc();
		$movie["rating-user"] = $row["rating"];	
	}
            
	$info = "<div class='page-header'> \n";
	$info .= "<h2>" . $movie["title"] . " <small>(" . $movie["year"] . ")</small><span class='pull-right'> \n";
	if ($movie["mpaarating"]) {
		$info .= "<small>Rated " . $movie["mpaarating"] . "</small> \n";
	}
	if ($movie["runningTime"]) {
		$info .= "<small>";
		if ($movie["mpaarating"]) {
			$info .= " &#8213; ";
		}
		$info .= $movie["runningTime"] . " minutes</small>";
	}
	$info .= "</span> \n";

	$info .= "</h2></div> \n";
	$info .= "<div class='row'> \n";
	$info .= "<div class='span8'>";
	if ($movie["genres"]) {
		$info .= "<p>" . join(", ", $movie["genres"]) . "</p> \n";
	} 
	if ($movie["directors"]) {
                $links = array();
                for ($i = 0; $i < count($movie["directors"]); $i++) {
                        array_push($links, "<a href='person.php?p=" . $movie["directors"][$i]["pid"] . "'>" . $movie["directors"][$i]["name"] . "</a>");
                }
                $info .= "<p>Directed by " . join(", ", $links) . "</p> \n";
        }

        if ($movie["producers"]) {
                $links = array();
                for ($i = 0; $i < count($movie["producers"]); $i++) {
                        array_push($links, "<a href='person.php?p=" . $movie["producers"][$i]["pid"] . "'>" . $movie["producers"][$i]["name"] . "</a>");
                }
                $info .= "<p>Produced by " . join(", ", $links) . "</p> \n";
        }

	$info .= "</div> \n ";

                $info .= "<div class='span4'><div class='pull-right'> \n";
	
		if ($movie["rating-count"]) {
	
		$info .= "<span class='rating-numbers'>" . $movie["rating-count"] . " rating";
		if ($movie["rating-count"] > 1) {
			$info .= "s";
		}

		$info .= "<span class='rating-score";
		if ($movie["rating-avg"] >= 7) {
			$info .= " text-success";
		} else if ($movie["rating-avg"] >= 5) {
			$info .= " text-warning";
		} else if ($movie["rating-avg"] >= 3) {
			$info .= " text-error";
		}
		$info .= "'><strong>" . $movie["rating-avg"] . "</strong></span></span>";

		}

		if (isset($_COOKIE["username"])) {
                        $info .= "<form class='rating-form pull-right'><input type='hidden' name='m' value='" . $movie["mid"] . "'> \n";
                        $info .= "<div class='input-append'><select class='input-mini' name='rate'><option>#</option> \n";
                        for ($i = 1; $i <= 10; $i++) {
                                if ($movie["rating-user"] == $i) {
                                       $info .= "<option value='$i' selected>$i</option> \n";
                                } else {
                                        $info .= "<option value='$i'>$i</option> \n";
                                }
                        }
                        $info .= "</select><button type='submit' class='btn'>Rate</button></div></form> \n";
                }

		$info .= "</span></div></div> \n";

	$info .= "</div> \n";
	
	if ($movie["actors"]) {
		$info .= "<table class='table'> \n";
		$info .= "<thead><tr><th>Cast</th></tr></thead> \n";
		$info .= "<tbody> \n";
		for ($i = 0; $i < count($movie["actors"]); $i++) {
			$info .= "<tr><td><a href='person.php?p=" . $movie["actors"][$i]["pid"] . "'>" . $movie["actors"][$i]["name"] . "</a></td>";
			$info .= "<td><p class='text-right'>" . $movie["actors"][$i]["role"] . "</p></td></tr> \n";
		}
		$info .= "</tbody></table> \n";
	}

	/*
	if ($movie["keywords"]) {
		$info .= "<h4>Keywords</h4><p class='muted'><small>" . join(", ", $movie["keywords"]) . "</small></p> \n";
	}
	*/

	$info .= "</div> \n";

	echo $info;

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

		$search = "";

		if ($response->num_rows == 0) {
			$search .= "<div class='text-center'> \n";
			$search .= "<h2>No results found!</h2> \n";
			$search .= "<p>The search term you are looking for is in another castle.</p> \n";
			$search .= "</div> \n";
		} else {
			$search .= "<div class='search-results'> \n";
			$search .= "<h2>Search results for " . $_GET["q"] . "</h2> \n";
			$search .= "<table class='table table-striped table-hover'> \n";
			$search .= "<thead> <tr> <th>Title</th> <th>Year</th> <th>Running time</th> </tr> </thead> \n";
			$search .= "<tbody> \n";

			while ($row = $response->fetch_assoc()) {
				$search .= "<tr> <td><a href='movie.php?m=" . $row["mid"] . "'>" . $row["title"] . "</a></td> <td>" . $row["year"] . "</td> <td>" . $row["runningTime"] . "</td> </tr> \n";	
			}

			$search .= "</tbody> </table> \n";
		}
		
		echo $search;
        }
}

	?>
		</div>

    </body>
</html>
