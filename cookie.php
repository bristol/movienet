<?php

if (isset($_POST["deletecookie"])) {
	
	setcookie("username", "", time()-3600);
	unset($_COOKIE["username"]);

} else if ($_POST["uid"] != "" && $_POST["password"] != "") {
    	

	$db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    	if ($db->connect_errno) {
        	echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        	exit(1);
    	}
    
	$query = "select name, password from users where name='" . $_POST["email"] ."'";
    	
	$result = db->query($query);
	$result->data_seek(0);

    	if (!$result) {
      		echo "User not found!";
    	} else {
		$row = $result->fetch_assoc();

        	$name = $row[0];
        	$pass = $row[1];
        
		if ($pass == $_POST["password"]) {
          		setcookie("username", $name, time()+3600);
          		$_COOKIE["username"] = $name;
        	}
    	}
}

?>
