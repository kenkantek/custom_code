<?php
try
{

	require '../config.php';
	global $db;

	list($id, $billing_id) = explode('|', $_POST['p']);
	
	//anti sql injection
	$id = intval($id);
	$billing_id = intval($billing_id);
	
	//get amount
	$sql = "SELECT amount FROM payment WHERE id = $id";
	$result = $db->query_object($sql);
	if(!$result)
	{
		echo jsonEncode('Record does not exist!', false);
	}
	
	//update remain
	//update remain
	$query = "UPDATE billing SET remain_amount = remain_amount + {$result->amount}
					WHERe id = '$billing_id'";
	mysql_query($query);
	
	
	$sql = "DELETE FROM payment WHERE id = $id";
	$result = $db->execute($sql);
	
	echo jsonEncode('Delete record success!');
	
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}