<?php
/**
    Author: Vishvas Handa (100044749)
    Version: 1.0
    
    admin.php serves as the admin console of the cabsonline system, a admin is allowed to view unassigned bookings within next two hours and also to update the status of bookings to assigned.
*/
    include('config.php');
    session_start();
    $table = '';
    $err = '';
    if($_SESSION)
    {
        if($_POST)
        {
            if($_POST['update'] == 0)
            {
                $table = "<table border=1 width=100%>
        <tr>
            <th>Ref.#</th>
            <th>Customer Name</th>
            <th>Passenger Name</th>
            <th>Passenger Phone</th>
            <th>Pick-up Address</th>
            <th>Dstination Suburb</th>
            <th>Pick-up Time</th>
        </tr>";
                $query_select = "SELECT * FROM  `booking_table` WHERE `booking_status` = 'unassigned' AND `pickup_datetime` BETWEEN NOW() AND NOW() + INTERVAL 2 HOUR";
                $res = mysqli_query($db_conn, $query_select) or die("Select query failed: ".mysqli_error($db_conn));
                while($row = mysqli_fetch_array($res, MYSQL_ASSOC))
                {
                    $query_select2 = "SELECT `name` FROM `customer_table` WHERE `email` = '".$row['customer_email']."'";
                    $res2 = mysqli_query($db_conn, $query_select2) or die("Name select query failed: ".mysqli_error($db_conn));
                    $temp = mysqli_fetch_array($res2);
                    $c_name = $temp['name'];
                    mysqli_free_result($res2);
                    $address = fix_address($row['address_unit_number'], $row['address_street_number'], $row['address_street_name'], $row['address_suburb_name']);
                    $pickup_time = fix_datetime($row['pickup_datetime']);
                    $table.= "<tr>
                        <td>{$row['booking_number']}</td>
                        <td>{$c_name}</td>
                        <td>{$row['passenger_name']}</td>
                        <td>{$row['passenger_phone']}</td>
                        <td>{$address}</td>
                        <td>{$row['destination_suburb']}</td>
                        <td>{$pickup_time}</td>
                    </tr>";
                }
                $table.="</table>";
                mysqli_free_result($res);
            }
            if($_POST['update']==1)
            {
                if(isset($_POST['ref_no']) && $_POST['ref_no']!='')
                {
                    $ref_no = mysqli_real_escape_string($db_conn, $_POST['ref_no']);
                    $query_update = "UPDATE `booking_table` SET `booking_status` = 'assigned' WHERE `booking_number` = '$ref_no'";
                    mysqli_query($db_conn, $query_update);
                    if(mysqli_affected_rows($db_conn)>0)
                    {
                        $err = "The booking request number $ref_no has been properly assigned.<br>";
                    }
                    else
                    {
                        $err = "Update failed: The booking with request number $ref_no was not found.<br>";
                    }
                }
                else
                {
                    $err = "Please enter the reference number of a booking request.<br>";
                }
            }
            mysqli_close($db_conn);
        }
    }
    else
    {
        header("Location: login.php");
    }

    function fix_address($unit_num,$st_num,$st_name,$suburb)
    {
        $temp = "";
        if($unit_num!='')
        {
            $temp.= $unit_num."/";
        }
        $temp.= $st_num." ".$st_name.", ".$suburb;
        return $temp;
    }

    function fix_datetime($datetime)
    {
        return date_format(date_create($datetime), 'd M h:i');
    }
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Console</title>
    <style type="text/css">
        .logout_lable{
                float:right;
                margin-right:130px;
                margin-top:-70px;
            }
    </style>
</head>
<body>

    <h1>CabsOnline Admin Console.</h1>
    <a href="logout.php" class="logout_lable">Logout</a>
    <h3>1. Click button below to search for all unassigned booking requests with a pick-up time within 2 hours.</h3>
    <form method="post">
        <input type="hidden" name="update" value="0">
        <input type="submit" value="List All">
    </form>
    <br><br>
    <?php
        echo $table;
    ?>
    <hr>
    <br>
    <h3>2. Input a reference number below and click "update" button to assign a taxi to that request.</h3>
    <form method="post">
        <label for="ref_no">Reference Number:</label>
        <input type="text" name="ref_no">
        <input type="hidden" name="update" value="1">
        <input type="submit" value="Update">
    </form>
    <?php
        echo "<br><br><h4>".$err."</h4>";
    ?>
</body>
</html>