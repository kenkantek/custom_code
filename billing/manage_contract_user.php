<?php

	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		emptySession();
		header("Location: index.php");
		exit();
	}

	$smarty->assign('navTitle','Contract User');
	$smarty->assign('processPath','contract_user_file_ajax/');
	$smarty->display('contract_user/index.html');
