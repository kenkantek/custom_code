<?php

try
{
	require '../config.php';

	$billing_id		= intval($_POST['billing_id']);
	$notes_id		= intval($_POST['notes_id']);

	$date			= date("Y-m-d", strtotime($_POST['date']));
	$notes			= mysql_real_escape_string($_POST['notes']);

	$query			= " UPDATE billing_notes SET
							`billing_id` = '$billing_id',
							`date` = '$date',
							`notes` = '$notes',
							`modified_by` = '{$user->id}',
							`date_modified` = now()
						WHERE id = '$notes_id'
						";

	$result = mysql_query($query);
	if($result == false)
	{
		echo jsonEncode('Cannot update notes. Query: '.$query, false);
		die;
	}

	echo jsonEncode('Update success');
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}