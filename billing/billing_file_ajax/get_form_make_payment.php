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
	list($contract_id, $month, $year, $bill_id) = explode('|', $param);
	
	if($contract_id == '' || $month == '' || $year == '' || $bill_id == '')
	{
		echo jsonEncode('Not enough info. Check params.');
		die;
	}
	
	//anti sql injection
	$contract_id	= intval($contract_id);
	$month			= intval($month);
	$year			= intval($year);
	$bill_id		= intval($bill_id);
	
	//check if existed in billing, don't do
	$query = "SELECT billing.*,
						DATE_FORMAT(billing.date_verified,'%m/%d/%Y') AS date_verify,
						contract_user.username
					FROM billing
						INNER JOIN contract_user
							ON billing.verified_by = contract_user.id
				WHERE billing.month = $month
						AND billing.year = $year
						AND billing.contract_id = $contract_id
						AND billing.id = $bill_id";
	$result = mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Can not query to check billing detail. Debug Query : '.$query);
		die;
	}
	if(mysql_num_rows($result)== 0)
	{
		echo jsonEncode('Billing not existed, cannot make payment for it.');
		die;
	}
	$billDetails = mysql_fetch_assoc($result);
	$billDetails['month'] = getMonthName($billDetails['month']);
	
	//select contract info
	//need check user admin?
	//if user not admin, maybe need check user->id = contract.assigned_user
	//else not show
	if($user->type == ADMIN || $user->type == STAFF)
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
		echo jsonEncode('Can not query contract details. Debug Query : '.$query);
		die;
	}
	if(mysql_num_rows($result) == 0)
	{
		echo jsonEncode('Contract not existed or you not authorized to view this contract');
		die;
	}
	
	$contractDetails = mysql_fetch_assoc($result);
	
	$smarty->assign('bill', $billDetails);
	$smarty->assign('contract', $contractDetails);
	
		
	echo jsonEncode($smarty->fetch('billing/form_make_payment.html'));
}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getMessage(), false);
}