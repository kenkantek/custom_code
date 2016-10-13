<?php
	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		header("Location: index.php");
		exit();
	}

	$smarty->assign('userCombo', getUserCombo('assignedUserSearch', 'comboSmall'));

	//calculate year, we begin from 2011
	$fromYear = YEAR_BEGIN;
	//end year is current year
	$endYear = date('Y');

	$years = array();
	for($i = $fromYear; $i <= $endYear ; $i++)
	{
		$years[] = '<option value="'.$i.'">'.$i.'</option>';
	}
	$smarty->assign('yearOptions', implode('',$years));

	$smarty->assign('navTitle','Contract Billing');
	$smarty->assign('processPath','billing_file_ajax/');
	$smarty->display('billing/index.html');
