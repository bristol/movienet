<?php require_once("config.php"); ?>
<?php include_once("cookie.php"); ?>

<?php 

	if (!isset($_COOKIE["username"]) || $_COOKIE["username"] != "admin") {
		echo "Hahaha what do you even think you're doing kid";
		exit(0);
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

	<form method="post">
		<textarea name='query' placeholder='Enter your sicknasty query here' rows="4" cols="100"></textarea>
		<button type='submit' class='btn btn-primary'>Run that</button>
	</form>

	<?php

		if (isset($_POST["query"])) {
			
			$response = $db->query($_POST["query"]);
			$response->data_seek(0);

			$input = "<div class='well'> \n";
			$input .= "<code>" . $_POST["query"] . "</code> \n";
			$input .= "</div> \n";

			$status = "<div class='well'> \n";
			$status .= "<code>" . $db->info . "</code> \n";
			if ($db->errno) {
				$status .= "<code>" . $db->errno . ": " . $db->error . "</code> \n";
			} else {
				$status .= "<code>OK</code> \n";
			}
			$status .= "</div>";

			$output = "<div class='well'> \n";
			$output .= "<code>";
			while ($row = $response->fetch_array(MYSQLI_NUM)) {
				$fields = "";
				for ($i = 0; $i < count($row); $i++) {
					$fields .= $row[$i] . "\t";
				}
				$output .= $fields . "\n";
			}
			$output .= "</code> \n";
			$output .= "</div> \n";

			echo $input;
			echo $status;
			echo $output;
		}

	?>			

    </body>
</html>
