<?php require_once("config.php"); ?>
<?php include_once("login.php"); ?>

<?php 

	//connect to the db
    	$db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    	if ($db->connect_errno) {
        	echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        	exit(1);
    	}

	$k = 5;
	if (isset($_GET["k"])) {
		$k = $_GET["k"];
	}

	$statement = "select M.mid, M.title, round(avg(R.rating), 2) from movies M, rated R where M.mid=R.mid group by mid having count(R.rating) > 100 order by
`round(avg(R.rating), 2)` desc limit $k;";
	$response = $db->query($statement);
	$response->data_seek(0);
	$movies = array();
	while ($row = $response->fetch_assoc()) {
		array_push($movies, array("mid" => $row["mid"], "title" => $row["title"], "rating" => $row["round(avg(R.rating), 2)"]));
	}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>The best movies on Movienet</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>

	<div class='container'>
		<h1>Top Movies</h1>
		<div class='row'>
			<div class='span10'>
				<p>Showing the top <?php echo $k ?> movies on Movienet. I guess you could say that means they're pretty good or something but
that's just what I hear. I mean obviously Dude Where's My Car was the best movie ever made but whatever.</p>
			</div>
			<div class='span2'>
				<div class='pull-right'>
					<form>
						<div class='input-append'>
							<input type='number' name='k' value='<?php echo $k ?>'class='input-mini' min='1'>
							<button class='btn' type='submit'>Show</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<table id='ratingtable' class='table table-striped table-hover'>
			<thead>
				<tr>
					<th>Rank</th>
					<th>Title</th>
					<th><p class='text-right'>Rating</p></th>
				</tr>
			</thead>
			<tbody>
				<?php 
					for ($i = 1; $i <= $k; $i++) { ?> 
						<tr>
							<td><?php echo $i ?></td>
							<td><a href='movie.php?m=<?php echo $movies[$i - 1]["mid"] ?>'><?php echo $movies[$i - 1]["title"] ?></a></td>
							<td><p class='text-right'><?php echo $movies[$i - 1]["rating"] ?></p></td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
    </body>
</html>
