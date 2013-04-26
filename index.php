<?php require_once("config.php"); ?>
<?php include_once("login.php"); ?>

<?php 

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
        <title>Movienet</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>

	<div class='container'>
		<div class='hero-unit'>
		<?php
			if (isset($_COOKIE["username"])) {
		?>
			<h2>You're logged in!</h2>
		<?php
			} else {
		?>
			<h1>Movienet</h1>
			<p>Movienet is like, a place where you and your friends can like, talk about movies and stuff and like oh my god they have all these movies
there's like a million of them and you can like rate each movie and then it changes the overall rating for the movie so everyone else will see your ratings and it
makes me feel like I don't know maybe I could be like a movie critic or something wait are you even listening?</p>
			<a href='signup.php' class='btn btn-primary btn-large'>Sign Up</a>
		<?php } ?>
			
		</div>			
	</div>
    </body>
</html>
