<?php
	require_once 'config.php';

	if(isset($_POST['submit']))
	{
		$username	= $_POST['username'];
		$password	= $_POST['password'];

		$form = new Form_ContractUser();
		$form->username = $username;
		$form->password = $password;
		
		if($user = $form->checkUser())
		{
			$_SESSION['userInfo'] = serialize($user);
			header("Location: manage_billing.php");
			exit();
		}
		else
		{
			$message = urlencode('Wrong Username or Password');
			header("Location: index.php?m=$message");
			exit();
		}
	}

	