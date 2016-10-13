<?php
	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		header("Location: index.php");
		exit();
	}

	$smarty->assign('regionCombo', getRegionCombo());

	//calculate year, we begin from 2011
	$smarty->assign('currentYear',date('Y'));
	$smarty->assign('currentMonth',date('m') - 1);

	$smarty->assign('navTitle','Reports');
	$smarty->assign('processPath','report_file_ajax/');
	$smarty->display('report/index.html');
