<?php

try
{
	require '../config.php';

	$facilityIds	= $_POST['desc'];
	$number			= mysql_real_escape_string($_POST['number']);
	$name			= mysql_real_escape_string($_POST['name']);
	$start_date		= date("Y-m-d", strtotime($_POST['start_date']));
	$assigned_user	= $_POST['assigned_user'];

	$query			= " INSERT INTO contract SET
							name = '$name',
							number = '$number',
							assigned_user = '$assigned_user',
							start_date = '$start_date'
						";

	$result = mysql_query($query);
	if($result == false)
	{
		echo jsonEncode('Can not insert to contract',false);
		die;
	}

	$mainId	= mysql_insert_id();

	$temp = array();
	foreach($facilityIds as $id)
	{
		$temp[] = "($mainId, $id)";
	}

	$query = " INSERT INTO contract_facility(contract_id, facility_id) VALUES ".implode(',', $temp);
	mysql_query($query);

	echo jsonEncode('Add success');
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}