<?php

require_once '../config.php';

try
{
	//connect database
	connectDatabase();

	$username	= escapseString($_POST['username']);
	$password	= md5($_POST['password']);

	$query		= "SELECT * FROM users
						WHERE username = '$username' AND password = '$password' AND status = 0";
	$result		= mysql_query($query);

	//close connection
	closeConnection();

	if(!$result || mysql_num_rows($result) == 0)
	{
		echo jsonEncode('Wrong info', false);
	}
	else
	{
		$row = mysql_fetch_assoc($result);
		$_SESSION['userInfo'] = serialize($row);

		echo jsonEncode('Okay, logged');
	}
}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}