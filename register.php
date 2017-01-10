<?php
/**
    Author: Vishvas Handa (100044749)
    Version: 1.0
    
    register.php is used to register new users in the system. The data given by the users is validated and stored to database before they are redirected to the booking page to allow them to make booking for a cab.
*/
	include("config.php");
	session_start();
	$err = "";
	if($_POST)
	{
		if(isset($_POST['name']) && $_POST['name']!='' && isset($_POST['email']) && $_POST['email']!='' && isset($_POST['pass']) && $_POST['pass']!='' && isset($_POST['pass2']) && $_POST['pass2']!='' && isset($_POST['phone']) && $_POST['phone']!='')
		{
			if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			{
				$err .= "Please enter a valid email address.<br>";
			}
			if($_POST['pass'] != $_POST['pass2'])
			{
				$err .= "The entered passwords do not match.<br>";
			}
			if(!is_numeric($_POST['phone']))
			{
				$err.= "Please enter a numeric phone number.";
			}
			if($err=="")
			{
				$email = mysqli_real_escape_string($db_conn, $_POST['email']);
				$querySel = "select `name` from `customer_table` where `email` = '$email'";
				$res = mysqli_query($db_conn, $querySel) or die("Lookup Query Failed: ".mysqli_error($db_conn));
				

				if(!mysqli_num_rows($res)>0)
				{
					$name = mysqli_real_escape_string($db_conn, $_POST['name']);
					$pass = mysqli_real_escape_string($db_conn, $_POST['pass']);
					$phone = mysqli_real_escape_string($db_conn, $_POST['phone']);
					$queryIns = "INSERT INTO `customer_table` (`name`, `password`, `email`, `phone`) VALUES ('$name', '$pass', '$email', '$phone')";
					if(mysqli_query($db_conn, $queryIns) or die("Insert query failed: ".mysqli_error($db_conn)))
					{
						$_SESSION['user_email'] = $email;
						$_SESSION['user_name'] = $name;
						header("Location: booking.php");
					}
					else
					{
						$err = "There was some problem with the registration process, please try later.";
					}
				}
				else
				{
					$err = "The email address is already registered.";
				}
				mysqli_free_result($res);
			}
		}
		else{
			$err="All feilds are mandatory!<br>";
		}
	}
	mysqli_close($db_conn);
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
		<h2>Register to CabsOnline!</h2><br/>
		<p>Please fill in the details below to complete your registration:</p><br/>
		<form method="post">
			<div class="feild">
				<label for="name">Name:</label><span>
				<input id="name" type="text" name="name"></span>
			</div>
			<div class="feild">
				<label>Password:</label>
				<span><input type="password" name="pass"></span>
			</div>
			<div class="feild">
				<label>Confirm Password:</label>
				<span><input type="password" name="pass2"></span>
			</div>
			<div class="feild">
				<label>Email address:</label>
				<span><input type="email" name="email"></span>
			</div>
			<div class="feild">
				<label>Phone:</label>
				<span><input type="tel" name="phone"></span>
			</div>
			<br>
			<div class="feild">
				<span><input type="submit" value="Register"/></span>
			</div>
		</form>
		<p>Already registered? <a href="login.php" id="register">Login here</a></p><br><br>
		<?php
			echo $err;
		?>
	</body>
</html>

