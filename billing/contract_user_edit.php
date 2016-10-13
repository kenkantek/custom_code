<?php

	//header
	require_once 'config.php';
	$db = new db($servername, $dbusername, $dbpassword, $dbname);
	
	$id = 0;
	
	if(array_key_exists('id',$_REQUEST)){
		$id = (int)$_REQUEST["id"];
	}
	
	$form = new Form_ContractUser();
	
	if ($db->query_exist("select id from contract_user where id = $id")){

		$object = $db->query_object("select * from contract_user where id = $id limit 1");

		$form->id = $object->id;
		
		$form->username = $object->username;

		$form->password = $object->password;
		
	}else{
		
	}
	
	$smarty->assign('id',$id);
	
	$smarty->assign('form',$form);

	$smarty->assign('navTitle','Contract User');
	
	$smarty->assign('processPath','contract_file_ajax');
	
	$smarty->display('contract_user/edit.html');
