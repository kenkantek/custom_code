<?php
	ini_set('display_errors',1);

	require_once 'config.php';

	//block direct access
	if(!$user)
	{
		header("Location: index.php");
		exit();
	}

	//do we need only admin can access this page ? yes --> must add code check is ADMIN

	$param = $_GET['p'];
	list($contract_id, $month, $year) = explode('|', $param);

	if($contract_id == '' || $month == '' || $year == '')
		die('Not enough info. Check params.');


	//anti sql injection
	$contract_id	= intval($contract_id);
	$month			= intval($month);
	$year			= intval($year);

	//check if existed in billing, don't do
	$query = "SELECT * FROM billing
				WHERE month = $month AND year = $year AND contract_id = $contract_id";
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query to check billing detail. Debug Query : '.$query);
	}
	if(mysql_num_rows($result) == 0)
	{
		die('Billing not existed');
	}
	$billDetails = mysql_fetch_assoc($result);

	//check if need unverify && make sure current user is SUPER
	if(isset($_POST['unverify']) && $user->type == SUPER)
	{
		$billing_id = $billDetails['id'];

		$query = "DELETE FROM billing
				WHERE month = $month AND year = $year AND contract_id = $contract_id";
		$result = mysql_query($query);

		$query = "DELETE FROM payment
				WHERE billing_id = '$billing_id'";
		$result = mysql_query($query);

		$query = "DELETE FROM billing_notes
				WHERE billing_id = '$billing_id'";
		$result = mysql_query($query);

		//reset content
		$content = '<html>
					<head>
					<script type="text/javascript">alert("Bill unverified success");</script>
					<script type="text/javascript">window.opener.list();</script>
					<script type="text/javascript">window.close();</script>
					</head>
					<body>Bill unverified success</body>
					</html>';
		echo $content;
		die;
	}

	//select contract info
	//need check user admin?
	//if user not admin, maybe need check user->id = contract.assigned_user
	//else not show
	if($user->type == ADMIN || $user->type == STAFF || $user->type == SUPER)
	{
		$query = "SELECT *
						FROM contract WHERE id = '$contract_id'";
	}
	else
	{
		$query = "SELECT *
						FROM contract WHERE id = '$contract_id' AND assigned_user = '$user->id'";
	}
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query contract details. Debug Query : '.$query);
	}
	if(mysql_num_rows($result) == 0)
	{
		die('Contract not existed or you not authorized to view this contract');
	}
	$contractDetails = mysql_fetch_assoc($result);

	//we get this content from db to log
	$content = $billDetails['log'];

	$smarty->assign('headTitle', 'Billing Detail');

	//check if need send email
	if(isset($_POST['resend']))
	{
		if($contractDetails['file_format'] == '' || $contractDetails['file_format'] == 'pdf')
		{
			//file name
			$file_name = TEMPORARY_PATH.date('YmdHis').'.pdf';
			exportPDF($content, $file_name);
			$fileForEmail = "Billing_Invoice_{$month}_{$year}_resend.pdf";
		}
		elseif($contractDetails['file_format'] == 'doc')
		{
			//file name
			$file_name = TEMPORARY_PATH.date('YmdHis').'.doc';
			exportDOC($content, $file_name, 'F');
			$fileForEmail = "Billing_Invoice_{$month}_{$year}_resend.doc";
		}
		else
		{
			//file name
			$file_name = TEMPORARY_PATH.date('YmdHis').'.xls';
			exportXLS($content, $file_name, 'F');
			$fileForEmail = "Billing_Invoice_{$month}_{$year}_resend.xls";
		}

		//cc
		$cc = array();
		if($contractDetails['email2'] != '')
			$cc[] = $contractDetails['email2'];
		if($contractDetails['email3'] != '')
			$cc[] = $contractDetails['email3'];

		include 'smtp_config.php';
		$check = sendSMTPMail($contractDetails['email'],
								MAIL_SUBJECT_BILLING,
								MAIL_BODY_BILLING,
								$file_name,
								$cc,
								$fileForEmail);
		if($check === true)
		{
			//reset content
			$contentExtra = 'Resend email with billing file '.$fileForEmail.' attach success<br/>';
		}
		else
		{
			//reset content
			$contentExtra = 'Cannot send email.<br/>
							Error : '.$check.'<br/>';
		}
		$smarty->assign('contentExtra', $contentExtra);
	}

	//check if need download
	if(isset($_POST['download']))
	{
		if(!isset($_POST['file_format']) || $_POST['file_format'] == 'pdf')
		{
			//file name, if download just the name, not full path
			$file_name = "Billing_Invoice_{$month}_{$year}.pdf";
			exportPDF($content, $file_name, 'D');
		}
		elseif($_POST['file_format'] == 'doc')
		{
			//file name, if download just the name, not full path
			$file_name = "Billing_Invoice_{$month}_{$year}.doc";
			exportDOC($content, $file_name);
		}
		else
		{
			//file name, if download just the name, not full path
			$file_name = "Billing_Invoice_{$month}_{$year}.xls";
			exportXLS($content, $file_name);
		}

		die;
	}

	//assign right to un-verify
	$smarty->assign('unverifyRight', $user->type == SUPER);

	//show file format
	$smarty->assign('fileFormat', getFileFormatText($contractDetails['file_format']));

	//now display it
	$smarty->assign('content', $content);
	$smarty->assign('contract', $contractDetails);
	$smarty->display('billing/detail_for_view.html');