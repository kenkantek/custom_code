<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$id	= escapseString($_POST['id']);

	$query	= " SELECT * FROM cars WHERE id = '$id' ";
	$result = mysql_query($query);

	if($result == false || mysql_num_rows($result) == 0)
	{
		echo jsonEncode('Record not exsited. Query: '.$query, false);
		die;
	}
	$row		= mysql_fetch_assoc($result);

	$files		= array();

	if($row['image'] != '') $files[] = IMAGE_PATH.$row['image'];

	//delete gallery/ need add more to $image array
	$query	= " SELECT * FROM car_gallery WHERE id_car = '$id' ";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{
			if($row['image'] != '') $files[] = IMAGE_PATH.$row['image'];
		}

		$query	= " DELETE FROM car_gallery WHERE id_car = '$id' ";
		$result = mysql_query($query);
	}

	//delete properties
	$query	= "DELETE FROM car_properties WHERE id_car = '$id' ";
	$result = mysql_query($query);

	//remove main
	$query	= "DELETE FROM cars WHERE id = '$id' ";
	$result = mysql_query($query);

	//close
	closeConnection();

	//remove files
	foreach($files AS $path)
	{
		@unlink($path);
	}

	echo jsonEncode('Record was removed success');

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}