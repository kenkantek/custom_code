<?php
	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		header("Location: index.php");
		exit();
	}

	$smarty->assign('userCombo', getUserCombo('assignedUserSearch', 'comboSmall'));
	
	//get file format combo for search
	$smarty->assign('fileFormatCombo', getFileFormatCombo('', 'fileFormatSearch', 'comboSmall', true));

	$smarty->assign('navTitle','Contract');
	$smarty->assign('processPath','contract_file_ajax/');
	$smarty->display('contract/index.html');
