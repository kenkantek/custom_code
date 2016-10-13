<?php
	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		header("Location: index.php");
		exit();
	}

	$smarty->assign('userCombo', getUserCombo('assignedUserSearch', 'comboSmall'));

	//calculate year, we begin from 2011 to current year
	$currentYear = date('Y');
	$year = array('<select id="currentYear" name="currentYear" class="comboSmall">');
	for($i = YEAR_BEGIN ; $i <= $currentYear ; $i++)
	{
		if($i == $currentYear)
			$year[] = "<option value='$i' selected='selected'>$i</option>";
		else
			$year[] = "<option value='$i'>$i</option>";
	}
	$year[] = '</select>';

	$smarty->assign('currentYear',implode('', $year));

	$smarty->assign('navTitle','Contract Billing');
	$smarty->assign('processPath','billing_file_ajax/');
	$smarty->display('billing/index.html');
