<?php

try
{
	require '../config.php';

	$form = new Form_ContractUser();
	$form->process(array_merge($_POST,$_GET));

	if(!$form->hasError())
	{
		echo jsonEncode('Add record success!');
	}
	else
	{
		echo jsonEncode($form->getErrors(), false);
	}
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}