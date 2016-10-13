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

	$billing_id = intval($_GET['billing_id']);
	$smarty->assign('billing_id', $billing_id);
	
	echo jsonEncode($smarty->fetch('billing/form_add_note.html'));
}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getMessage(), false);
}