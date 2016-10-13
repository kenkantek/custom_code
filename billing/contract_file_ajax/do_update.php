<?php

try
{
	require '../config.php';

	$id				= $_POST['recordId'];

	//remove old
	$query = " DELETE FROM contract_facility WHERE contract_id = '$id'";
	mysql_query($query);

	$facilityIds	= $_POST['desc'];
	$number			= mysql_real_escape_string($_POST['number']);
	$name			= mysql_real_escape_string($_POST['name']);
	$email			= mysql_real_escape_string($_POST['email']);
	$email2			= mysql_real_escape_string($_POST['email2']);
	$email3			= mysql_real_escape_string($_POST['email3']);
	$title			= mysql_real_escape_string($_POST['title']);
	$address		= mysql_real_escape_string($_POST['address']);
	$phone			= mysql_real_escape_string($_POST['phone']);
	$tax			= mysql_real_escape_string($_POST['tax']);
	$region			= mysql_real_escape_string($_POST['region']);
	$start_date		= date("Y-m-d", strtotime($_POST['start_date']));
	$assigned_user	= $_POST['assigned_user'];
	$file_format	= $_POST['file_format'];

	$query			= " UPDATE contract SET
							name = '$name',
							number = '$number',
							email = '$email',
							email2 = '$email2',
							email3 = '$email3',
							title = '$title',
							address = '$address',
							phone = '$phone',
							tax = '$tax',
							region = '$region',
							assigned_user = '$assigned_user',
							start_date = '$start_date',
							file_format = '$file_format'
						WHERE id = '$id'
						";

	$result = mysql_query($query);
	if($result == false)
	{
		echo jsonEncode('Can not update data of contract',false);
		die;
	}

	$mainId	= $id;

	$temp = array();
	foreach($facilityIds as $id)
	{
		$temp[] = "($mainId, $id)";
	}

	$query = " INSERT INTO contract_facility(contract_id, facility_id) VALUES ".implode(',', $temp);
	mysql_query($query);

	echo jsonEncode('Update success');
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}