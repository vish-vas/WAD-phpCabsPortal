<?php
/**
Author: Vishvas Handa (100044749)
Version: 1.0

config.php contains the login credentials for the database and creates a connection for the application to use.
*/
   define('DB_SERVER', 'mysql.ict.swin.edu.au');
   define('DB_USERNAME', 's100044749');
   define('DB_PASSWORD', '100044749');
   define('DB_DATABASE', 's100044749_db');
   $db_conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Connection failed: ".mysqli_connect_error());
?>