<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$param	= explode('|', $_POST['param']);
	$id		= escapseString($param[0]);
	$status	= escapseString($param[1]);

	$query	= " UPDATE users SET status = '$status' WHERE id = '$id' ";
	$result = mysql_query($query);

	//close
	closeConnection();

	if($result == false)
	{
		echo jsonEncode('Cannot update record. Query: '.$query, false);
		die;
	}

	echo jsonEncode('Record was changes status success');

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}