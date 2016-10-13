<?php

	require_once 'config.php';
	
	emptySession();

	if(isset($_GET['m']))
	{
		$smarty->assign('message', $_GET['m']);
	}

	$smarty->display('index.html');
