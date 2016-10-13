<?php
try
{

	require '../config.php';
	global $db;

	$id = intval($_POST['id']);
	
	//$sql = "DELETE FROM contract_facility WHERE contract_id = $id";
	
	//$sql = "DELETE FROM contract_facility WHERE contract_id = $id";
	
	$result = $db->execute($sql);

	//$sql = "DELETE FROM contract WHERE id = $id";
	$sql = "UPDATE contract 
			SET status = 1
			WHERE id = $id";

	$result = $db->execute($sql);
	
	echo jsonEncode('Delete record success!',true);
	
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}