<?php
try
{
	require_once '../config.php';

	//block direct access
	if(!$user)
	{
		echo jsonEncode('TIME_OUT');
		die;
	}
	
	//do we need only admin can access this page ? yes --> must add code check is ADMIN
	
	$param = $_GET['p'];
	list($payment_id, $billing_id) = explode('|', $param);
	
	if($payment_id == '' || $billing_id == '')
	{
		echo jsonEncode('Not enough info. Check params.');
		die;
	}
	
	//anti sql injection
	$payment_id	= intval($payment_id);
	
	//check if existed in billing, don't do
	$query = "SELECT *,
				DATE_FORMAT(date,'%m/%d/%Y') AS date_formmatted
				FROM payment
				WHERE id = '$payment_id'";
	$result = mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Can not query to get payment detail. Debug Query : '.$query);
		die;
	}
	if(mysql_num_rows($result)== 0)
	{
		echo jsonEncode('Payment not exist, cannot edit.');
		die;
	}
	$row = mysql_fetch_assoc($result);
	
	$smarty->assign('payment', $row);
	$smarty->assign('billing_id', $billing_id);
	$smarty->assign('payment_id', $payment_id);
		
	echo jsonEncode($smarty->fetch('billing/form_edit_payment.html'));
}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getMessage(), false);
}