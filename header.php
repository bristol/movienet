<div class='navbar navbar-inverse navbar-fixed-top'>
    <div class='navbar-inner'>
        <div class='container'>
		<a class='brand' href='index.php'>Movienet</a>
        	<ul class='nav'>
            		<li>
                		<a href='movie.php'>Movies</a>
            		</li>
            		<li>
                		<a href='person.php'>Artists</a>
            		</li>
			<li>
				<a href='topmovies.php'>Top Movies</a>
			</li>
            <?php 
		if (isset($_COOKIE["username"])) {
			echo "<li> <a href='user.php?u=" . $_COOKIE["uid"] . "'> " . $_COOKIE["username"] . " </a> </li> ";

			if ($_COOKIE["username"] == "admin") {
				echo "<li> <a href='admin.php'>Administration</a> </li> \n";
			}

		}

		?>

        	</ul>

	<?php

		if (isset($_COOKIE["username"])) {

			$banner = "<form method='post' class='navbar-form pull-right'> ";
			$banner .= "<input name='deletecookie' value='true' type='hidden'> ";
			$banner .= "<button type='submit' class='btn'>Log out</button> ";
			$banner .= "</form>";

			echo $banner;
		} else {

			$banner = "<form method='post' class='navbar-form pull-right'> ";
              		$banner .= "<input name='email' class='span3' type='text' placeholder='Email'> ";
              		$banner .= "<input name='password' class='span3' type='password' placeholder='Password'> ";
              		$banner .= "<button type='submit' class='btn'>Log in</button> ";
			$banner .= "</form> ";

			echo $banner;
		}

	?>
	</div>
    </div>
</div>
