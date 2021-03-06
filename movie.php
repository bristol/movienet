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

		$statement = "select * from rated where mid=$mid and uid=" . $_COOKIE["uid"] . ";";
		$response = $db->query($statement);
		$oldrating = 0;
		if ($response && $response->num_rows > 0) {
			$response->data_seek(0);
			$row = $response->fetch_assoc();
			$oldrating = $row["rating"];
		}

		$statement = "select * from movies where mid=" . $mid . ";";
		$response = $db->query($statement);
		$response->data_seek(0);
		$row = $response->fetch_assoc();

		if ($oldrating) {
			$newcount = $row["countRatings"];
			$newavg = round(((($row["avgRating"] * $row["countRatings"]) - $oldrating + $_GET["rate"]) / $newcount), 2);
		} else {
			$newcount = $row["countRatings"] + 1;
			$newavg = round(((($row["avgRating"] * $row["countRatings"]) + $_GET["rate"]) / $newcount), 2);
		}

		$statement = "insert into rated (uid, mid, rating) values (" . $_COOKIE["uid"] . ", " . $mid . ", " . $_GET["rate"] . ") on duplicate key update
rating=" . $_GET["rate"] . ";";
		$response = $db->query($statement);

		$statement = "update movies set avgRating=$newavg, countRatings=$newcount where mid=$mid;";
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
	
	$statement = "select P.pid, P.name, A.role from acted_in A, people P where A.mid=" . $movie["mid"] . " and A.pid=P.pid;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$actors = array();
	while ($row = $response->fetch_assoc()) {
		array_push($actors, array("name" => $row["name"], "pid" => $row["pid"], "role" => $row["role"]));
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
		?>

	<div class='hero-unit'>
		<p>Search for movies on Movienet</p>
		<div id='search-simple'>
			<form class='form-search'>
				<input type='text' name='q' class='input-xxlarge' placeholder='Name of movie'>
				<button type='submit' class='btn btn-primary'>Search</button>
			</form>
		</div>
		<div id='search-advanced'>
			<form class='form-horizontal'>
				<input type='hidden' name='advanced' value='true'>
				<div class='control-group'>
					<div class='controls'>
						<select id='search-field-select'>
							<option value='default' selected>Add a search field</option>
							<option value='search-title-group'>Title</option>
							<option value='search-year-group'>Year</option>
							<option value='search-mpaarating-group'>MPAA Rating</option>
							<option value='search-genre-group'>Genre</option>
							<option value='search-keyword-group'>Keyword</option>
							<option value='search-avgrating-group'>Average Rating</option>
							<option value='search-numrating-group'>Number of Ratings</option>
						</select>
					</div>
				</div>
				<div class='control-group' id='search-title-group'>
					<label class='control-label' for='search-title'>Title</label>
					<div class='controls'>
						<input type='text' class='advanced-input' id='search-title' name='search-title' placeholder='The Godfather'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group' id='search-year-group'>
					<label class='control-label' for='search-year-start'>Year (start, end)</label>
					<div class='controls'>
						<input type='number' class='input-small advanced-input' id='search-year-start' name='search-year-start' placeholder='1970'>
						<input type='number' class='input-small advanced-input' id='search-year-end' name='search-year-end' placeholder='2001'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-mpaarating-group'>
					<label class='control-label' for='search-mpaarating'>MPAA Rating</label>
					<div class='controls'>
						<select class ='input-medium advanced-input' name='search-mpaarating' id='search-mpaarating'>
							<option value='' selected>Select a rating</option>
							<option value='any'>Any</option>
							<option value='G'>G</option>
							<option value='PG'>PG</option>
							<option value='PG-13'>PG-13</option>
							<option value='R'>R</option>
							<option value='NC-17'>NC-17</option>
						</select>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-genre-group'>
					<label class='control-label' for='search-genre'>Genre</label>
					<div class='controls'>
						<select class='input-medium advanced-input' name='search-genre' id='search-genre'>
							<option value=''>Select a genre</option>
							<?php
								$response = $db->query("select genre from genres;");
								$response->data_seek(0);
								while ($row = $response->fetch_assoc()) {
									echo "<option value='" . $row["genre"] . "'>" . $row["genre"] . "</option> \n";
								}
							?>
						</select>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-keyword-group'>
					<label class='control-label' for='search-keyword'>Keyword</label>
					<div class='controls'>
						<input type='text' class='advanced-input' id='search-keyword' name='search-keyword'
placeholder='action-adventure'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-avgrating-group'>
					<label class='control-label' for='search-avgrating-low'>Average Rating (low, high)</label>
					<div class='controls'>
						<input type='number' class='input-mini advanced-input' min='1' max='10' step='any' id='search-avgrating-low' name='search-avgrating-low'
placeholder='2.5'>
						<input type='number' class='input-mini advanced-input' min='1' max='10' step='any' id='search-avgrating-high' name='search-avgrating-high'
placeholder='9.75'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-numrating-group'>
					<label class='control-label' for='search-numrating-low'>Number of Ratings (low, high)</label>
					<div class='controls'>
						<input type='number' class='input-small advanced-input' min='0' step='1' id='search-numrating-low' name='search-numrating-low'
placeholder='5'>
						<input type='number' class='input-small advanced-input' min='0' step='1' id='search-numrating-high' name='search-numrating-high'
placeholder='5000'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group'>
					<div class='controls'>
						<button type='submit' class='btn btn-primary'>Search</button>
					</div>
				</div>
			</form>
		</div>
		<a class='btn' id='search-toggle'>Advanced Search</a>
	</div>

	<?php

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
				$search .= "<tr> <td><a href='movie.php?m=" . $row["mid"] . "'>" . $row["title"] . "</a></td> <td>" . $row["year"] . "</td><td>";
 				if ($row["runningTime"]) {
					$search .= $row["runningTime"] . " min";
				}
				"</td> </tr> \n";
			}

			$search .= "</tbody> </table> \n";
		}
		
		echo $search;
        }

	if (isset($_GET["advanced"])) {
		$select = array();
		$from = array();
		$where = array();

		array_push($select, "M.mid", "M.title", "M.year", "M.runningTime");
		array_push($from, "movies M");

		if ($_GET["search-title"]) {
			array_push($where, "M.title like '%" . $_GET["search-title"] . "%'");
		}
		
		if ($_GET["search-year-start"]) {
			array_push($where, "M.year>=" . $_GET["search-year-start"]);
		}

		if ($_GET["search-year-end"]) {
			array_push($where, "M.year<=" . $_GET["search-year-end"]);
		} 

		if ($_GET["search-mpaarating"]) {
			array_push($select, "MA.abbreviation");
			array_push($from, "has_mpaa HA", "mpaaratings MA");
			array_push($where, "M.mid=HA.mid", "HA.mpaaid=MA.mpaaid");
			if ($_GET["search-mpaarating"] != "any") {
				array_push($where, "MA.abbreviation like '" . $_GET["search-mpaarating"] . "'");
			}
		}

		if ($_GET["search-genre"]) {
			array_push($select, "G.genre");
			array_push($from, "has_genre HG", "genres G");
			array_push($where, "M.mid=HG.mid", "HG.gid=G.gid", "G.genre like '" . $_GET["search-genre"] . "'");
		}

		if ($_GET["search-keyword"]) {
			array_push($select, "K.keyword");
			array_push($from, "has_key HK", "keywords K");
			array_push($where, "M.mid=HK.mid", "HK.kid=K.kid", "K.keyword like '%" . $_GET["search-keyword"] . "%'");
		}

		if ($_GET["search-avgrating-low"] || $_GET["search-avgrating-high"]) {
			array_push($select, "M.avgRating");
		
			if ($_GET["search-avgrating-low"]) {
				array_push($where, "M.avgRating>=" . $_GET["search-avgrating-low"]);
			}
			if ($_GET["search-avgrating-high"]) {
				array_push($where, "M.avgRating<=" . $_GET["search-avgrating-high"]);
			}
		}

		if ($_GET["search-numrating-low"] || $_GET["search-numrating-high"]) {
			array_push($select, "M.countRatings");
			
			if ($_GET["search-numrating-low"]) {
				array_push($where, "M.countRatings>=" . $_GET["search-numrating-low"]);
			}

			if ($_GET["search-numrating-high"]) {
				array_push($where, "M.countRatings<=" . $_GET["search-numrating-high"]);
			}
		}

		$query = "select " . join(", ", $select) . " from " . join(", ", $from) . " where " . join(" and ", $where) . ";";
		//echo $query;
	
		$response = $db->query($query);

		$search = "";

		if (!$response || $response->num_rows == 0) {
                        $search .= "<div class='text-center'> \n";
                        $search .= "<h2>No results found!</h2> \n";
                        $search .= "<p>Try using less search terms</p> \n";
                        $search .= "</div> \n";
                } else {

			$search .= "<div class='search-results'> \n";
			$search .= "<h4>Search results for ";
			
			$terms = array();
			$includes = array();
			if ($_GET["search-title"]) {
				array_push($terms, "title '" . $_GET["search-title"] . "'");
			}
			if ($_GET["search-mpaarating"]) {
				array_push($terms, "MPAA rating '" . $_GET["search-mpaarating"] . "'");
				array_push($includes, array("label" => "MPAA Rating", "field" => "abbreviation"));
			}
			if ($_GET["search-genre"]) {
				array_push($terms, "genre '" . $_GET["search-genre"] . "'");
				array_push($includes, array("label" => "Genre", "field" => "genre"));
			}
			if ($_GET["search-keyword"]) {
				array_push($terms, "keyword '" . $_GET["search-keyword"] . "'");
				array_push($includes, array("label" => "Keyword", "field" => "keyword"));
			}
			if ($_GET["search-year-start"] && $_GET["search-year-end"]) {
				array_push($terms, "year between " . $_GET["search-year-start"] . " and " . $_GET["search-year-end"]);
			} else if ($_GET["search-year-start"]) {
				array_push($terms, "year after " . $_GET["search-year-start"]);
			} else if ($_GET["search-year-end"]) {
				array_push($terms, "year before " . $_GET["search-year-end"]);
			}

			if ($_GET["search-avgrating-low"] || $_GET["search-avgrating-high"]) {
				array_push($includes, array("label" => "Average Rating", "field" => "avgRating"));
			}
			if ($_GET["search-avgrating-low"] && $_GET["search-avgrating-high"]) {
				array_push($terms, "average rating between " . $_GET["search-avgrating-low"] . " and " . $_GET["search-avgrating-high"]);
			} else if ($_GET["search-avgrating-low"]) {
				array_push($terms, "average rating higher than " . $_GET["search-avgrating-low"]);
			} else if ($_GET["search-avgrating-high"]) {
				array_push($terms, "average rating lower than " . $_GET["search-avgrating-high"]);
			}

			if ($_GET["search-numrating-low"] || $_GET["search-numrating-high"]) {
				array_push($includes, array("label" => "Number of ratings", "field" => "countRatings"));
			}
			if ($_GET["search-numrating-low"] && $_GET["search-numrating-high"]) {
                                array_push($terms, "between " . $_GET["search-numrating-low"] . " and " . $_GET["search-numrating-high"] . " ratings");
                        } else if ($_GET["search-numrating-low"]) {
                                array_push($terms, "more than " . $_GET["search-numrating-low"] . " ratings");
                        } else if ($_GET["search-numrating-high"]) {
                                array_push($terms, "less than " . $_GET["search-numrating-high"] . " ratings");
                        }


			$search .= join(" and ", $terms);
			$search .= "</h4> \n";

			$search .= "<table class='table table-striped table-hover'> \n";
			$search .= "<thead> \n";
			$search .= "<tr> \n";
			$search .= "<th>Title</th><th>Year</th>";
			for ($i = 0; $i < count($includes); $i++) {
				$search .= "<th>" . $includes[$i]["label"] . "</th>";
			}
			$search .= "</tr> \n";
			$search .= "</thead> \n";

			$search .= "<tbody> \n";

			$response->data_seek(0);
                        while ($row = $response->fetch_assoc()) {
				$search .= "<tr> \n";
				$search .= "<td><a href='movie.php?m=" . $row["mid"] . "'>" . $row["title"] . "</a></td><td>" . $row["year"] . "</td>";
				foreach ($includes as $include) {
					$search .= "<td>" . $row[$include["field"]] . "</td>";
				}
				$search .= "</tr> \n";
                        }

			$search .= "</tbody> \n";

			$search .= "</table> \n";

			$search .= "</div> \n";

		}

		echo $search;
	}
}

	?>
		</div>

	<?php include_once("jquery.php"); ?>
    </body>
</html>
