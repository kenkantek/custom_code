<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$id	= escapseString($_POST['id']);

	$query	= " SELECT * FROM car_gallery WHERE id = '$id' ";
	$result = mysql_query($query);

	if($result == false || mysql_num_rows($result) == 0)
	{
		echo jsonEncode('Record not exsited. Query: '.$query, false);
		die;
	}
	$row		= mysql_fetch_assoc($result);

	if($row['image'] != '')
	{
		@unlink(GALLERY_IMAGE_PATH.$row['image']);
		@unlink(GALLERY_IMAGE_PATH.'thumb_'.$row['image']);
	}

	//remove main
	$query	= "DELETE FROM car_gallery WHERE id = '$id' ";
	$result = mysql_query($query);

	//close
	closeConnection();

	echo jsonEncode('Record was removed success');

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}