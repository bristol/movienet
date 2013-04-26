<?php require_once("config.php"); ?>
<?php include_once("login.php"); ?>

<?php

	$newaccountfailed = false;

	//if the user logs in on this page and didn't create an account redirect them to the home page 
	if (isset($_COOKIE["username"]) && !isset($_POST["signup-name"])) {
		header("Location: index.php");
		exit(0);
	}

	//connect to the db
    	$db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    	if ($db->connect_errno) {
        	echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        	exit(1);
    	}

	//if the user submitted the signup form, create a user for them
      	if (isset($_POST["signup-name"])) { 
             	$statement = "insert into users (name, password, email";
              	$values = "('" . $_POST["signup-name"] . "', '" . $_POST["signup-password"] . "', '" . $_POST["signup-email"] . "'";
    

                if (isset($_POST["signup-age"])) {
               		$statement .= ", age";
                       	$values .= ", '" . $_POST["signup-age"] . "'";
            	}   

              	if (isset($_POST["signup-gender"])) {
                	$statement .= ", gender";
                       	$values .= ", '" . $_POST["signup-gender"] . "'";
              	}   

               	if (isset($_POST["signup-location"])) {
                  	$statement .= ", location";
                      	$values .= ", '" . $_POST["signup-location"] . "'";
             	}   

               	$values .= ")";
               	$statement .= ") values " . $values . ";";
               	$response = $db->query($statement);

		if (!$response || $db->errno) {
			$newaccountfailed = true;
		} else {
			//automatically log user in on successful account creation and redirect them to home page

			$statement = "select uid, name from users where email like '" . $_POST["signup-email"] . "';";
                        $response = $db->query($statement);
                        $response->data_seek(0);
                        $row = $response->fetch_assoc();

			setcookie("username", $row["name"], time()+3600);
                        $_COOKIE["username"] = $row["name"];
                        setcookie("uid", $row["uid"], time()+3600);
                        $_COOKIE["uid"] = $row["uid"];

			header("Location: index.php");
			exit(0);
		}
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

		<?php if ($newaccountfailed) { ?>
			<div class="alert">
  				Oh god, something went wrong. Try again.
			</div>
		<?php } ?>

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
