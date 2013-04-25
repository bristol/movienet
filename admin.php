<?php require_once("config.php"); ?>
<?php include_once("cookie.php"); ?>

<?php 

	if (!isset($_COOKIE["username"]) || $_COOKIE["username"] != "admin") {
		echo "Hahaha what do you even think you're doing kid";
		exit(0);
	}

	//connect to the db
    $db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    if ($db->connect_errno) {
        echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        exit(1);
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>You are admin! How did you get so admin!?</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>
	<div class='hero-unit'>
	<form method="post">
		<textarea name='query' placeholder='Enter your sicknasty query here' rows="4" cols="500"></textarea>
		<button type='submit' class='btn btn-primary'>Run that</button>
	</form>

	<?php

		if (isset($_POST["query"])) {
			
			$response = $db->query($_POST["query"]);

			$input = "<div class='well'> \n";
			$input .= "<code>" . $_POST["query"] . "</code> \n";
			$input .= "</div> \n";

			$status = "<div class='well'> \n";
			$status .= "<code>" . $db->info . " \n";
			if ($db->errno) {
				$status .= $db->errno . ": " . $db->error . " \n";
			} else {
				$status .= "OK \n";
			}
			$status .= "</code></div>";

			$output = "<div class='well'> \n";
			
			if ($response) {
				if (gettype($response) == "object") {
					$response->data_seek(0);
					while ($row = $response->fetch_array(MYSQLI_NUM)) {
						$fields = "";
						for ($i = 0; $i < count($row); $i++) {
							$fields .= $row[$i] . "\t";
						}
						$output .= "<code>" . $fields . "</code><br> \n";
					}
				} else {
					$output .= "<code>" . $response . "</code>";
				}
			}
			
			$output .= "</div> \n";

			echo $input;
			echo $status;
			echo $output;
		}

	?>			
	</div>
    </body>
</html>
