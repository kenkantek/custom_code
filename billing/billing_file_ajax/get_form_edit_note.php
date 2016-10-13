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
	list($notes_id, $billing_id) = explode('|', $param);
	
	if($notes_id == '' || $billing_id == '')
	{
		echo jsonEncode('Not enough info. Check params.');
		die;
	}
	
	//anti sql injection
	$notes_id	= intval($notes_id);
	
	$query = "SELECT *,
				DATE_FORMAT(date,'%m/%d/%Y') AS date_formmatted
				FROM billing_notes
				WHERE id = '$notes_id'";
	$result = mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Can not query to get notes detail. Debug Query : '.$query);
		die;
	}
	if(mysql_num_rows($result)== 0)
	{
		echo jsonEncode('Notes not exist, cannot edit.');
		die;
	}
	$row = mysql_fetch_assoc($result);
	
	$smarty->assign('record', $row);
	$smarty->assign('billing_id', $billing_id);
	$smarty->assign('notes_id', $notes_id);
		
	echo jsonEncode($smarty->fetch('billing/form_edit_note.html'));
}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getMessage(), false);
}