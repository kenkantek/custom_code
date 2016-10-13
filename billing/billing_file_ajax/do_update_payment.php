<?php

try
{
	require '../config.php';

	$billing_id		= intval($_POST['billing_id']);
	$payment_id		= intval($_POST['payment_id']);
	$amount			= floatval($_POST['amount']);

	//get old amount
	$query			= "SELECT amount FROM payment WHERE id = '$payment_id'";
	$result			= mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Cannot get payment detail. Query :'. $query, false);
		die;
	}
	$paymentDetails = mysql_fetch_assoc($result);

	$query			= "SELECT remain_amount FROM billing WHERE id = '$billing_id'";
	$result			= mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Cannot get bill detail. Query :'. $query, false);
		die;
	}
	$billDetails = mysql_fetch_assoc($result);

	$amountAvaible = $billDetails['remain_amount'] + $paymentDetails['amount'];

	if($amount > $amountAvaible)
	{
		echo jsonEncode('Amount too big. Remain amount can be used : $'. $amountAvaible, false);
		die;
	}

	$date			= date("Y-m-d", strtotime($_POST['date']));
	$check			= mysql_real_escape_string($_POST['check']);

	$query			= " UPDATE payment SET
							`billing_id` = '$billing_id',
							`date` = '$date',
							`amount` = '$amount',
							`check` = '$check',
							`modified_by` = '{$user->id}',
							`date_modified` = now()
						WHERE id = '$payment_id'
						";

	$result = mysql_query($query);
	if($result == false)
	{
		echo jsonEncode('Cannot update payment. Query: '.$query, false);
		die;
	}

	//update remain
	$query = "UPDATE billing SET remain_amount = ".($amountAvaible - $amount)."
				WHERE id = '$billing_id'";
	mysql_query($query);

	echo jsonEncode('Update success');
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}