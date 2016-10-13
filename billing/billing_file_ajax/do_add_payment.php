<?php

try
{
	require '../config.php';
	
	$billing_id		= intval($_POST['billing_id']);
	$contract_id	= intval($_POST['contract_id']);
	$amount			= floatval($_POST['amount']);
	
	$query			= "SELECT remain_amount FROM billing WHERE id = '$billing_id'";
	$result			= mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Cannot get bill detail. Query :'. $query, false);
		die;
	}
	$billDetails = mysql_fetch_assoc($result);
	if($amount > $billDetails['remain_amount'])
	{
		echo jsonEncode('Amount too big. Remain amount need paid is $'. $billDetails['remain_amount'], false);
		die;
	}

	$date			= date("Y-m-d", strtotime($_POST['date']));
	$check			= mysql_real_escape_string($_POST['check']);
	
	$query			= " INSERT INTO payment SET
							`billing_id` = '$billing_id',
							`date` = '$date',
							`amount` = '$amount',
							`check` = '$check',
							`created_by` = '{$user->id}',
							`date_created` = now()
						";

	$result = mysql_query($query);
	if($result == false)
	{
		echo jsonEncode('Cannot insert to payment. Query: '.$query, false);
		die;
	}
	
	//update remain
	$query = "UPDATE billing SET remain_amount = ROUND(remain_amount - $amount,2)
				WHERe id = '$billing_id'";
	mysql_query($query);

	//for notes
	$notes			= mysql_real_escape_string($_POST['notes']);
	if($notes != '')
	{
		$query			= " INSERT INTO billing_notes SET
							`billing_id` = '$billing_id',
							`date` = '$date',
							`notes` = '$notes',
							`created_by` = '{$user->id}',
							`date_created` = now()
						";

		$result = mysql_query($query);
	}
	
	echo jsonEncode('Add success');
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}