<?php

	//header
	require_once 'config.php';
	
	$smarty->assign('navTitle','Contract User');
	
	$smarty->assign('processPath','contract_file_ajax');
	
	$smarty->display('contract_user/add.html');
