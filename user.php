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

	$friender = false;
	$friended = false;

	if (isset($_COOKIE["uid"]) && $_COOKIE["uid"] != $_GET["u"]) {
		

		if (isset($_GET["friend"]) && $_GET["friend"]) {
			$datetime = date('Y-m-d H:i:s');
			$statement = "insert ignore into friends (uidFriender, uidFriended, requestTime) values (" . $_COOKIE["uid"] . ", " . $_GET["u"]
. ", '" . $datetime . "');";
			$response = $db->query($statement);
		}

		if (isset($_GET["unfriend"]) && $_GET["unfriend"]) {
			$statement = "delete from friends where (uidFriender=" . $_COOKIE["uid"] . " and uidFriended=" . $_GET["u"] . ") or
(uidFriender=" . $_GET["u"] . " and uidFriended=" . $_COOKIE["uid"] . ");";
			$response = $db->query($statement);
		}

		$statement = "select * from friends where (uidFriender=" . $_GET["u"] . " and uidFriended=" . $_COOKIE["uid"] . ") or (uidFriender=" .
$_COOKIE["uid"] . " and uidFriended=" . $_GET["u"] . ");";
		$response = $db->query($statement);

		if ($response && $response->num_rows > 0) {
			$response->data_seek(0);
			while($row = $response->fetch_assoc()) {
				if ($row["uidFriender"] == $_COOKIE["uid"]) {
					$friender = true;
				}
				if ($row["uidFriended"] == $_COOKIE["uid"]) {
					$friended = true;
				}
			}
		}

	}

	$statement = "select * from users where uid=" . $_GET["u"] . ";";
	$response = $db->query($statement);
	if ($response && $response->num_rows > 0) {
		$response->data_seek(0);
		$row = $response->fetch_assoc();
		?>

		<h2><?php echo $row["name"]; 
					if ($friender && $friended) {
						echo "<span class='label label-info'>Is your friend</span> \n";
					} else if ($friended) {
						echo "<a href='user.php?u=" . $_COOKIE["uid"] . "'><span class='label label-info'>Requested you as a
friend</span></a>";
					}?></h2>
		<div class='row'>
			<div class='span6'>
				<dl class='dl-horizontal'>
					<dt>Email</dt><dd><?php echo $row["email"]; ?></dd>
					<?php
						if ($row["age"]) {
							echo "<dt>Age</dt><dd>" . $row["age"] . "</dd> \n";
						}
						if ($row["location"]) {
							echo "<dt>Location</dt><dd>" . $row["location"] . "</dd> \n";
						}
						if ($row["gender"]) {
							echo "<dt>Gender</dt><dd>" . $row["gender"] . "</dd> \n";
						}
					?>
				</dl>
				<?php 
					if (isset($_COOKIE["uid"]) && $_COOKIE["uid"] != $row["uid"]) {
						

						if ($friender && $friended) {
							?>
								<form>  
                                                        		<input type='hidden' name='u' value='<?php echo $_GET["u"]; ?>'>
                                                        		<input type='hidden' name='unfriend' value='true'>
                                                        		<button type='submit' class='btn btn-primary'>Unfriend</button>
                                                		</form>
							<?php
						} else if ($friender) {
							?>
								<button type='button' disabled>Friend Request Sent</button>
							<?php
						} else if ($friended) {
							?>
							<form>  
                                                            <input type='hidden' name='u' value='<?php echo $_GET["u"]; ?>'>
                                                                <input type='hidden' name='friend' value='true'>
                                                                <button type='submit' class='btn btn-primary'>Accept Request</button>
                                                       	</form>
							<?php

						} else {
							?>
							<form>
                                                        	<input type='hidden' name='u' value='<?php echo $_GET["u"]; ?>'>
                                                        	<input type='hidden' name='friend' value='true'>
                                                        	<button type='submit' class='btn btn-primary'>Friend Request</button>
                                                	</form>
							<?php
						}
					}
				?>
			</div>
			<div class='span6'>
				<?php
				if (isset($_COOKIE["uid"]) && $_COOKIE["uid"] == $_GET["u"]) {

					$friended_users = array();
					$friender_users = array();
					$friends = array();
					$requests = array();

					//people who have friended you
					$statement1 = "select U.uid, U.name, U.email from users U, friends F where U.uid=F.uidFriender and F.uidFriended=" .
$_COOKIE["uid"]  . ";";
					$response1 = $db->query($statement1);

					//people who you have friended
					$statement2 = "select U.uid, U.name, U.email from users U, friends F where U.uid=F.uidFriended and F.uidFriender=" .
$_COOKIE["uid"] . ";";
					$response2 = $db->query($statement2);

					if ($response1 && $response1->num_rows > 0 && $response2 && $response2->num_rows > 0) {
						$response1->data_seek(0);
						while ($row = $response1->fetch_assoc()) {
							$friender_users[$row["uid"]] = array("uid" => $row["uid"], "name" => $row["name"], "email" => $row["email"]);
						}
						$response2->data_seek(0);
                                                while ($row = $response2->fetch_assoc()) {
                                                        $friended_users[$row["uid"]] = array("uid" => $row["uid"], "name" => $row["name"], "email" => $row["email"]);
                                                }
						foreach ($friender_users as $key => $value) {
							if (array_key_exists($key, $friended_users)) {
								$friends[$key] = $value;
							} else {
								$requests[$key] = $value;
							}
						}
					}

					if ($friends) {
						?>
						<h4>Friends</h4>
						<table class='table'>
							<tbody>
								<?php
								foreach ($friends as $key => $value) {
									echo "<tr> \n";
									echo "<td><a href='user.php?u=" . $value["uid"] . "'>" . $value["name"] . "</a></td>
\n";
									echo "<td>" . $value["email"] . "</td> \n";
									echo "</tr> \n";
								}
								?>
							</tbody>
						</table>
						<?php
					} else {
						?>
						<h4>Friends</h4>
						<p>You have no friends. <a href='user.php'>Look for some.</a></p>
						<?php
					}
					
					if ($requests) {
					?>
					<h4>Friend Requests</h4>
						<table class='table'>
							<tbody>
							<?php 
								foreach($requests as $key => $value) {
									echo "<tr> \n";
									echo "<td><a href='user.php?u=" . $value["uid"] . "'>" . $value["name"] . "</a></td> \n";
									echo "<td>" . $value["email"] . "</td> \n";
									echo "</tr> \n";
								}
							?>
							</tbody>
						</table>
					<?php
					}
				}
				?>
			</div>
		</div>

		<?php
	} else {
		?>
		<div class='text-center'>
			<h2>We couldn't find that user.</h2>
			<p>You can try <a href='user.php'>searching</a> for them.</p>
		</div>
		<?php
	}

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

		$where = array();
		$terms = array();

		if ($_GET["search-name"]) {
			array_push($where, "name like '" . $_GET["search-name"] . "'");
			array_push($terms, "name '" . $_GET["search-name"] . "'");
		}
		if ($_GET["search-email"]) {
			array_push($where, "email like '" . $_GET["search-email"] . "'");
			array_push($terms, "email address '" . $_GET["search-email"] . "'");
		}
		if ($_GET["search-age"]) {
			array_push($where, "age=" . $_GET["search-age"]);
			array_push($terms, "age " . $_GET["search-age"]);
		}
		if ($_GET["search-location"]) {
			array_push($where, "location like '" . $_GET["search-location"] . "'");
			array_push($terms, "located in '" . $_GET["search-location"] . "'");
		}
		if (isset($_GET["search-gender"]) && $_GET["search-gender"]) {
			array_push($where, "gender like '" . $_GET["search-gender"] . "'");
			array_push($terms, $_GET["search-gender"] . " gender");
		}

		$statement = "select * from users where " . join(" and ", $where) . ";";
		$response = $db->query($statement);

		echo "<h4>Search results for " . join(" and ", $terms) . "</h4> \n";

		if ($response) {
			?>
			<table class='table table-striped table-hover'>
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Age</th>
						<th>Location</th>
						<th>Gender</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$response->data_seek(0);
						while($row = $response->fetch_assoc()) {
							echo "<tr> \n";
							echo "<td><a href='user.php?u='" . $row["uid"] . "'>" . $row["name"] . "</a></td> \n";
							echo "<td>" . $row["email"] . "</td> \n";
							echo "<td>" . $row["age"] . "</td> \n";
							echo "<td>" . $row["location"] . "</td> \n";
							echo "<td>" . $row["gender"] . "</td> \n";
							echo "</tr> \n";
						}
					?>
				</tbody>
			</table>
			<?php
		} else {
			?>
			<div class='text-center'>
				<h2>No results found!</h2>
				<p>Better luck next time</p>
			</div>
			<?php
		}
	}
}

	?>
		</div>

	<?php include_once("jquery.php"); ?>
    </body>
</html>
