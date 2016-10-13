<?php
try
{

	require '../config.php';
	global $db;

	$id = intval($_POST['id']);
	
	//check existed in contract_user
	$sql = "SELECT id FROM contract WHERE assigned_user = $id";
	
	if($db->query_exist($sql))
	{
		echo jsonEncode('User is assigned to a contract!. Can not deleted', false);
		die;
	}
	
	$sql = "DELETE FROM contract_user WHERE id = $id";
	$result = $db->execute($sql);
	
	echo jsonEncode('Delete record success!',true);
	
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}