<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$id	= escapseString($_POST['id']);

	//remove main
	$query	= "DELETE FROM users WHERE id = '$id' ";
	$result = mysql_query($query);

	//close
	closeConnection();

	echo jsonEncode('Record was removed success');

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}