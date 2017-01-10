<?php
/**
    Author: Vishvas Handa (100044749)
    Version: 1.0
    
    booking.php is used to facilitate booking feature of the CabsOnline application. The html used here presents user with a minimal interface to get the required inputs and the php scripts validate data, store them to database and gives confirmation upon sucessfull booking by showing status and sending an email to the user.
*/

	include("config.php");
	session_start();
	$err='';
	if($_SESSION)
	{
		echo "<h1>Hi {$_SESSION['user_name']}, How are you today?</h1>";
		if($_POST)
		{
			if(isset($_POST['passenger_name']) && $_POST['passenger_name']!='' && isset($_POST['passenger_phone']) && $_POST['passenger_phone']!='' && isset($_POST['street_number']) && $_POST['street_number']!='' && isset($_POST['street_name']) && $_POST['street_name']!='' && isset($_POST['suburb_name']) && $_POST['suburb_name']!='' && isset($_POST['dest_suburb_name']) && $_POST['dest_suburb_name']!='' && isset($_POST['pickup_date']) && $_POST['pickup_date']!='' && isset($_POST['pickup_time']) && $_POST['pickup_time']!='')
			{
				
				$pickup_date = mysqli_real_escape_string($db_conn, $_POST['pickup_date']);
				$pickup_time = mysqli_real_escape_string($db_conn, $_POST['pickup_time']);
				if(!is_numeric($_POST['passenger_phone']))
				{
					$err.= "Please enter a numeric phone number.";
				}
				if(!check_date($pickup_date))
				{
					$err.= "Please enter valid date in specified format.<br>";
				}
				if(!check_time($pickup_time))
				{
					$err.= "Please enter valid time in specified format.<br>";
				}
				if(check_date($pickup_date) && check_time($pickup_time))
				{
					if(!checkDateTimeIsValid($pickup_date, $pickup_time))
					{
						$err = "Booking can not be made for less than one hour from now.<br>";
					}
				}

				if($err=='')
				{
					$passenger_name = mysqli_real_escape_string($db_conn, $_POST['passenger_name']);
					$passenger_phone = mysqli_real_escape_string($db_conn, $_POST['passenger_phone']);
					$unit_number = mysqli_real_escape_string($db_conn, $_POST['unit_number']);
					$street_number = mysqli_real_escape_string($db_conn, $_POST['street_number']);
					$street_name = mysqli_real_escape_string($db_conn, $_POST['street_name']);
					$suburb_name = mysqli_real_escape_string($db_conn, $_POST['suburb_name']);
					$dest_suburb_name = mysqli_real_escape_string($db_conn, $_POST['dest_suburb_name']);
					$email = $_SESSION['user_email'];
					$pickup_datetime = $pickup_date." ".$pickup_time;
					$query_booking = "INSERT INTO `booking_table` (`customer_email`, `passenger_name`, `passenger_phone`, `address_unit_number`, `address_street_number`, `address_street_name`, `address_suburb_name`, `destination_suburb`, `pickup_datetime`, `booking_status`) VALUES ('$email', '$passenger_name', '$passenger_phone', '$unit_number', '$street_number', '$street_name', '$suburb_name', '$dest_suburb_name', '$pickup_datetime', 'unassigned')";
					if(mysqli_query($db_conn, $query_booking) or die("Insert query failed: ".mysql_error($db_conn)))
					{
						$err = "<h3>Thank you! Your booking reference number is ".mysqli_insert_id($db_conn).". We will pick up the passengers in front of your provided address at $pickup_time on $pickup_date.</h3>";
						send_email(mysqli_insert_id($db_conn), $pickup_date, $pickup_time);
					}
				}
			}
			else
			{
				$err = "Please fill all the mandatory feilds to submit the form.<br>";
			}
		}
	}
	else
	{
		header("Location: login.php");
	}

	function check_date($date)
	{
		//checks validity of date for specified format : YYYY-MM-DD
		$temp = explode('-', $date);
		if(checkdate($temp[1], $temp[2], $temp[0]))
			return true;
		else
			return false;
	}

	function check_time($time)
	{
		//checks validity of time for specified format : HH:MM 24hr
		$temp = explode(':', $time);
		if($temp[0]<0 || $temp[0]>23 || !is_numeric($temp[0]))
			return false;
		if($temp[1]<0 || $temp[1]>59 || !is_numeric($temp[1]))
			return false;
		return true;
	}
	//The function checkDateTimeIsValid is meant to chaeck if the date time entered by the user is in the future and not in the past.
	function checkDateTimeIsValid($date,$time)
	{
		$now = date_add(new DateTime(), date_interval_create_from_date_string('1 hour'));
		if($now > new DateTime($date.' '.$time))
			return false;
		return true;
	}

	function send_email($ref_no, $pick_date, $pick_time)
	{
		$subject = "Your booking request with CabsOnline!";
		$message = "Dear {$_SESSION['user_name']}, Thanks for booking with CabsOnline! Your booking reference number is $ref_no. We will pick up the passengers in front of your provided address at $pick_time on $pick_date.";
		$header = "From booking@cabsonline.com.au";
		mail($_SESSION['user_email'], $subject, $message, $header, "-r 1234567@student.swin.edu.au");
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Book a cab!</title>
	<style type="text/css">
			.feild > label{
				display: inline-block;
				width: 250px;
				vertical-align: top;
			}
			.inline_feild > label{
				display: inline-block;
				width: 140px;
				vertical-align: top;
                margin-left:110px;
			}
			.logout_lable{
				float:right;
				margin-right:130px;
				margin-top:-110px;
			}
	</style>
</head>
<body>

	<h2>Book a cab!</h2>
	<a href="logout.php" class="logout_lable">Logout</a>
	<p>Please fill the feilds below to book a taxi</p>
	<br>
	<form method="post">
		<div class="feild">
			<label>Passenger Name:</label>
			<span><input type="text" name="passenger_name"></span>
		</div>
		<div class="feild">
			<label>Contact phone of passenger:</label>
			<span><input type="tel" name="passenger_phone"></span>
		</div>
		<div class="feild">
			<label>Pick up address:</label>
			<div class="inline_feild">
				<label>Unit number:</label>
				<span><input type="text" name="unit_number" placeholder="(optional)"></span>
			</div>
			<div class="inline_feild">
				<label>Street number:</label>
				<span><input type="text" name="street_number"></span>
			</div>
			<div class="inline_feild">
				<label>Street name:</label>
				<span><input type="text" name="street_name"></span>
			</div>
			<div class="inline_feild">
				<label>Suburb:</label>
				<span><input type="text" name="suburb_name"></span>
			</div>
		</div>
		<div class="feild">
			<label>Destination suburb:</label>
			<span><input type="text" name="dest_suburb_name"></span>
		</div>
		<div class="feild">
			<label>Pickup date:</label>
			<span><input type="date" name="pickup_date" placeholder="YYYY-MM-DD"></span>
		</div>
		<div class="feild">
			<label>Pickup time:</label>
			<span><input type="time" name="pickup_time" placeholder="HH:MM (24Hr Format)"></span>
		</div>
		<br>
		<input type="submit" value="Book">
	</form>
	<br>
	<br>
	<?php
		echo $err;
	?>
</body>
</html>