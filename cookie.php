<?php

if (isset($_POST["deletecookie"])) {
	
	setcookie("username", "", time()-3600);
	unset($_COOKIE["username"]);
	setcookie("uid", "", time()-3600);
	unset($_COOKIE["uid"]);

} else if (isset($_POST["email"]) && isset($_POST["password"]) && $_POST["email"] != "" && $_POST["password"] != "") {
    	

	$db = new mysqli($mnconfig["host"], $mnconfig["user"], $mnconfig["password"], $mnconfig["db"]);
    	if ($db->connect_errno) {
        	echo "Failed to connect to db: $db->connect_errno $db->connect_error";
        	exit(1);
    	}
    
	$query = "select uid, name, password from users where email='" . $_POST["email"] ."'";
    	
	$result = $db->query($query);

    	if (!$result) {
      		echo "User not found!";
    	} else {
		$result->data_seek(0);
		$row = $result->fetch_assoc();

		$uid = $row['uid'];
        	$name = $row['name'];
        	$pass = $row['password'];
        
		if ($pass == $_POST["password"]) {
          		setcookie("username", $name, time()+3600);
          		$_COOKIE["username"] = $name;
			setcookie("uid", $uid, time()+3600);
			$_COOKIE["uid"] = $uid;

        	}
    	}
}

?>
