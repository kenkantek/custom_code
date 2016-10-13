<?php
	require_once '../config.php';

	global $db;

	$id = intval($_GET['id']);
		
	$form = new Form_ContractUser();
	
	if($db->query_exist("SELECT id FROM contract_user WHERE id = $id"))
	{
		$object			= $db->query_object("SELECT * FROM contract_user WHERE id = $id limit 1");
		$form->id		= $object->id;
		$form->username = $object->username;
		$form->password = $object->password;
		$form->type		= $object->type;
	}
	else
	{
		
	}
	
	$smarty->assign('id',$id);
	
	$smarty->assign('form',$form);

	echo jsonEncode($smarty->fetch('contract_user/form_edit.html'));
