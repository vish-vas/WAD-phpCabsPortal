<?php
/**
    Author: Vishvas Handa (100044749)
    Version: 1.0
    
    logout.php is used to remove user session credentials from the database and to redirect the user to login page.
*/
	session_start();
	if(session_destroy())
	{
		header("Location: login.php");
	}
?>
