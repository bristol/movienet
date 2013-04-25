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
        <title>Create an account on Movienet</title>

        <?php include_once("bootstrap.php"); ?>  
  
    </head>
    <body>

        <?php include_once("header.php"); ?>

	<div class='container'>
		<div class='hero-unit'>
		<h2>Get started on Movienet today!</h2>
			<form method='post' class='form-horizontal'>
				<div class='control-group'>
					<label class='control-label' for='signup-name'>Name</label>
					<div class='controls'>
						<input type='text' id='signup-name' name='signup-name' placeholder='Sue Bob' required>
					</div>
				</div>
				<div class='control-group'>
					<label class='control-label' for='signup-email'>Email</label>
					<div class='controls'>
						<input type='email' id='signup-email' name='signup-email' placeholder='suebob1999@yahoo.com' required>
					</div>
				</div>
				<div class='control-group'>
					<label class='control-label' for='signup-password'>Password</label>
					<div class='controls'>
						<input type='password' id='signup-password' name='signup-password' placeholder='ilovebradxoxo' required>
					</div>
				</div>
				<div class='control-group'>
					<label class='control-label' for='signup-location'>Location</label>
					<div class='controls'>
						<input type='text' id='signup-location' name='signup-location' placeholder='Boone County, WV'>
					</div>
				</div>
				<div class='control-group'>
					<label class='control-label' for='signup-age'>Age</label>
					<div class='controls'>
						<input type='number' id='signup-age' name='signup-age' placeholder='17'>
					</div>
				</div>
				<div class='control-group'>
					<label class='control-label'>Gender</label>
					<div class='controls'>
						<label class='radio inline'>
							<input type='radio' name='signup-gender' id='signup-gender-male' value='Male'>
							Male
						</label>
						<label class='radio inline'>
							<input type='radio' name='signup-gender' id='signup-gender-female' value='Female'>
							Female
						</label>
					</div>
				</div>
				<div class='control-group'>
					<div class='controls'>
						<label class='checkbox' for='aboutthatlife'>
							<input type='checkbox' id='signup-aboutthatlife' name='signup-aboutthatlife'>
							I'm about that life
						</label>
						<button type='submit' class='btn btn-primary'>Create Account</button>
					</div>
				</div>
			</form>
		</div>			
	</div>
    </body>
</html>
