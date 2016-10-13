<?php
try
{

	require '../config.php';

	//block direct access
	if(!$user)
	{
		echo jsonEncode('TIME_OUT');
		die;
	}

	//only admin can do
	if($user->type != ADMIN)
	{
		echo jsonEncode('Only Admin can delete notes record', false);
		die;
	}
	
	global $db;

	list($id, $billing_id) = explode('|', $_POST['p']);
	
	//anti sql injection
	$id = intval($id);
	$billing_id = intval($billing_id);
	
	$sql = "DELETE FROM billing_notes WHERE id = $id AND billing_id = $billing_id";
	$result = $db->execute($sql);
	
	echo jsonEncode('Delete record success!');
	
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}