<?php
/**
    Author: Vishvas Handa (100044749)
    Version: 1.0
    
    login.php is used to authenticate users by checking their user credentials against the data in the mysql database. upon succesful login user is redirected to booking page where they can make a new booking while the admin is redirected to the admin console.
*/
	include("config.php");
	session_start();
	$err = "";
	if($_POST)
	{
		if(isset($_POST['email']) && $_POST['email']!='' && isset($_POST['pass']) && $_POST['pass']!='')
		{
			if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			{
				$err = "Please enter a valid email address.<br>";
			}

			if($err=="")
			{
				$email = mysqli_real_escape_string($db_conn, $_POST['email']);
				$pass = mysqli_real_escape_string($db_conn, $_POST['pass']);
				$query_sel = "SELECT `name` FROM `customer_table` where `email` = '$email' AND `password` = '$pass'";
				$res = mysqli_query($db_conn, $query_sel) or die("Database select query failed: ".mysqli_error($db_conn));
				if(mysqli_num_rows($res)>0)
				{
					$row = mysqli_fetch_array($res, MYSQLI_ASSOC);
					$_SESSION['user_email'] = $email;
					$_SESSION['user_name'] = $row['name'];
					if($email=="admin@cabs.com")
						header("Location: admin.php");
					else
						header("Location: booking.php");
				}
				else
				{
					$err = "The email/ password combination was not found.";
				}
				mysqli_free_result($res);
			}
		}
		else
		{
			$err = "Please enter your email and password combination.<br>";
		}
		mysqli_close($db_conn);
	}

?>


<!DOCTYPE html>
<html>
	<head>
		<title>Taxi Booking App</title>
		<style type="text/css">
			.feild > label{
				display: inline-block;
				width: 150px;
				vertical-align: top;
			}
		</style>
	</head>
	<body>
	
		<form method="post">
			<h2>Login to CabsOnline!</h2>
			<br><br>
			<div class="feild">
			<label for="email">Email:</label>
			<span><input type="email" name="email"></span>
			</div>
			<div class="feild">
			<label for="pass">Password</label>
			<span><input type="password" name="pass"></span>
			</div>
			<br>
			<input type="submit" value="Login">
		</form>
		<p>New Member? <a href="register.php" id="login">Register here</a>
		</p><br><br>
		<?php
			echo $err;
		?>
	</body>
</html>

