<?php

try
{
	require '../config.php';
	
	$billing_id		= intval($_POST['billing_id']);
	//$contract_id	= intval($_POST['contract_id']);

	$date			= date("Y-m-d", strtotime($_POST['date']));
	$notes			= mysql_real_escape_string($_POST['notes']);
	
	$query			= " INSERT INTO billing_notes SET
							`billing_id` = '$billing_id',
							`date` = '$date',
							`notes` = '$notes',
							`created_by` = '{$user->id}',
							`date_created` = now()
						";

	$result = mysql_query($query);
	if($result == false)
	{
		echo jsonEncode('Cannot insert to billing_notes. Query: '.$query, false);
		die;
	}
	
	echo jsonEncode('Add success');
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}