<?php
	ini_set('display_errors',1);
	
	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		header("Location: index.php");
		exit();
	}
	
	//do we need only admin can access this page ? yes --> must add code check is ADMIN
	
	$param = $_GET['p'];
	list($contract_id, $month, $year, $bill_id) = explode('|', $param);
	
	if($contract_id == '' || $month == '' || $year == '' || $bill_id == '')
		die('Not enough info. Check params.');
	
	
	//anti sql injection
	$contract_id	= intval($contract_id);
	$month			= intval($month);
	$year			= intval($year);
	$bill_id		= intval($bill_id);
	
	//check if existed in billing, don't do
	$query = "SELECT * FROM billing
				WHERE month = $month
						AND year = $year
						AND contract_id = $contract_id
						AND id = $bill_id";
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query to check billing detail. Debug Query : '.$query);
	}
	if(mysql_num_rows($result)== 0)
	{
		die('Billing not existed, cannot make payment for it.');
	}
	
	//select contract info
	//need check user admin?
	//if user not admin, maybe need check user->id = contract.assigned_user
	//else not show
	if($user->type == ADMIN)
	{
		$query = "SELECT contract.*,
							contract_user.username
						FROM contract 
							INNER JOIN contract_user
									ON contract.assigned_user = contract_user.id
						WHERE contract.id = '$contract_id'";
	}
	else
	{
		$query = "SELECT contract.*,
							contract_user.username
						FROM contract
							INNER JOIN contract_user
									ON contract.assigned_user = contract_user.id
						WHERE contract.id = '$contract_id' AND contract.assigned_user = '$user->id'";
	}
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query contract details. Debug Query : '.$query);
	}
	if(mysql_num_rows($result) == 0)
	{
		die('Contract not existed or you not authorized to view this contract');
	}
	$contractDetails = mysql_fetch_assoc($result);
	
	echo 'we will show form for make payment here';

//	//now display it
//	$smarty->assign('content', $content);
//	$smarty->display('billing/detail_for_verify.html');