<?php require_once("config.php"); ?>
<?php include_once("login.php"); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Movienet Users</title>

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

    if (isset($_GET["u"])) {

    } else {
		?>

	<div class='hero-unit'>
		<p>Search for other users on Movienet</p>
		<div>
			<form class='form-horizontal'>
				<input type='hidden' name='advanced' value='true'>
				<div class='control-group'>
					<div class='controls'>
						<select id='search-field-select'>
							<option value='default' selected>Add a search field</option>
							<option value='search-name-group'>Name</option>
							<option value='search-email-group'>Email</option>
							<option value='search-age-group'>Age</option>
							<option value='search-gender-group'>Gender</option>
							<option value='search-location-group'>Location</option>
						</select>
					</div>
				</div>
				<div class='control-group' id='search-name-group'>
					<label class='control-label' for='search-name'>Name</label>
					<div class='controls'>
						<input type='text' class='advanced-input' id='search-name' name='search-name' placeholder='Bill Nye'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group' id='search-email-group'>
					<label class='control-label' for='search-email'>Email</label>
					<div class='controls'>
						<input type='email' class='advanced-input' id='search-email' name='search-email' placeholder='bill@pbs.org'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-age-group'>
					<label class='control-label' for='search-age'>Age</label>
					<div class='controls'>
						<input type='number' class='advanced-input' id='search-age' name='search-age' min='0' step='1' placeholder='21'>
						<a class='btn btn-danger btn-small search-hide'>&times;</a>
					</div>
				</div>
				<div class='control-group search-group-hidden' id='search-location-group'>
                                        <label class='control-label' for='search-location'>Location</label>
                                        <div class='controls'>
                                                <input type='text' id='search-location' name='search-location' class='advanced-input' placeholder='New York, NY'>
                                                <a class='btn btn-danger btn-small search-hide'>&times;</a>
                                        </div>
                                </div>
				<div class='control-group search-group-hidden' id='search-gender-group'>
					<div class='controls'>
                                                <label for='search-gender-male' class='radio inline'>
                                                        <input type='radio' name='search-gender' id='search-gender-male' class='advanced-input' value='Male'>
                                                        Male
                                                </label>
                                                <label class='radio inline' for='search-gender-female'>
                                                        <input type='radio' class='advanced-input' name='search-gender' id='search-gender-female' value='Female'>
                                                        Female
                                                </label>
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
	</div>

	<?php

	if (isset($_GET["advanced"])) {
		$select = array();
		$from = array();
		$where = array();
		$having = array();
		$groupby = array();
		$orderby = array();
		$limit = 0;

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
			array_push($select, "round(avg(R.rating), 2)");
			array_push($from, "rated R");
			array_push($where, "M.mid=R.mid");
			array_push($groupby, "mid");
		
			if ($_GET["search-avgrating-low"]) {
				array_push($having, "round(avg(R.rating), 2)>=" . $_GET["search-avgrating-low"]);
			}
			if ($_GET["search-avgrating-high"]) {
				array_push($having, "round(avg(R.rating), 2)<=" . $_GET["search-avgrating-high"]);
			}
		}

		if ($_GET["search-numrating-low"] || $_GET["search-numrating-high"]) {
			array_push($select, "count(R.rating)");
			
			if (!in_array("rated R", $from)) {
				array_push($from, "rated R");
				array_push($where, "M.mid=R.mid");
				array_push($groupby, "mid");
			}

			if ($_GET["search-numrating-low"]) {
				array_push($having, "count(R.rating)>=" . $_GET["search-numrating-low"]);
			}

			if ($_GET["search-numrating-high"]) {
				array_push($having, "count(R.rating)<=" . $_GET["search-numrating-high"]);
			}
		}

		$query = "select " . join(", ", $select) . " from " . join(", ", $from) . " where " . join(" and ", $where);
		if ($groupby) {
			$query .= " group by " . join(", ", $groupby);
		}
		if ($having) {
			$query .= " having " . join(" and ", $having);
		}
		if ($orderby) {
			$query .= " order by " . join(", ", $orderby);
		}
		$query .= ";";
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
				array_push($includes, array("label" => "Average Rating", "field" => "round(avg(R.rating), 2)"));
			}
			if ($_GET["search-avgrating-low"] && $_GET["search-avgrating-high"]) {
				array_push($terms, "average rating between " . $_GET["search-avgrating-low"] . " and " . $_GET["search-avgrating-high"]);
			} else if ($_GET["search-avgrating-low"]) {
				array_push($terms, "average rating higher than " . $_GET["search-avgrating-low"]);
			} else if ($_GET["search-avgrating-high"]) {
				array_push($terms, "average rating lower than " . $_GET["search-avgrating-high"]);
			}

			if ($_GET["search-numrating-low"] || $_GET["search-numrating-high"]) {
				array_push($includes, array("label" => "Number of ratings", "field" => "count(R.rating)"));
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
