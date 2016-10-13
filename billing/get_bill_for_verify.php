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

	$monthServer = date('n');
	$yearServer  = date('Y');
//	if($year != $yearServer)
//		die('Wrong year '.$year.'. We can only allow verify or view stat for current year');

	//check if existed in billing, don't do
	$query = "SELECT * FROM billing
				WHERE month = $month AND year = $year AND contract_id = $contract_id";
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query to check billing existed or not. Debug Query : '.$query);
	}
	if(mysql_num_rows($result) > 0)
	{
		die('Billing existed, cannot for verify');
	}

	//select contract info
	//need check user admin?
	//if user not admin, maybe need check user->id = contract.assigned_user
	//else not show
	if($user->type == ADMIN || $user->type == STAFF || $user->type == SUPER)
	{
		$query = "SELECT contract.*,
							contract_user.username
						FROM contract
							INNER JOIN contract_user
									ON contract.assigned_user = contract_user.id
						WHERE contract.id = '$contract_id'";
	}
	else
	{
		$query = "SELECT contract.*,
							contract_user.username
						FROM contract
							INNER JOIN contract_user
									ON contract.assigned_user = contract_user.id
						WHERE contract.id = '$contract_id' AND contract.assigned_user = '$user->id'";
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


	//we should cache all proceduremanagement to php array
	//instead query for each procedure of each order, will make slow than this
	$procedureManagementInfo = array();
	$procedureCategory = array();

	$query = "SELECT * FROM tblproceduremanagment";
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result))
	{
		if($row['fldCategory'] != '')
		{
			$procedureManagementInfo[$row['fldDescription']] = $row;
			$procedureCategory[$row['fldCategory']] = 0;
		}
	}


	//select all facilities details
	$query = "SELECT F.*
				FROM tblfacility AS F
				INNER JOIN contract_facility AS CF
					ON CF.facility_id = F.fldID
					AND CF.contract_id = '$contract_id'";
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query facility details. Debug Query : '.$query);
	}
	if(mysql_num_rows($result) == 0)
	{
		die('Not Facility assigned for this Contract. ContactId = '.$contract_id);
	}


	//now fetch data for cached
	$facilityInfoCache = array();
	$facilityNames = array();
	$facilityOfContract = array();
	while($row = mysql_fetch_assoc($result))
	{
		$facilityInfoCache[$row['fldFacilityName']] = $row;
		$facilityNames[] = "'{$row['fldFacilityName']}'";

		//used for check which facilies don't have order
		$facilityOfContract[$row['fldFacilityName']] = 1;
	}


	//now select order details
	//remember select based condition fldSchDate month and year
	//also, passed record which had fldException3 != ''
	$query = "SELECT *,
					DATE_FORMAT(fldSchDate,'%m-%d-%Y %H:%s') AS fldSchDateForLog,
					DATE_FORMAT(fldSchDate,'%m-%d-%Y') AS fldSchDateFormatted
				FROM tblorderdetails
					WHERE (fldException3 = '' OR fldException3 is NULL)
						AND MONTH(fldSchDate) = $month AND YEAR(fldSchDate) = $year
						AND fldFacilityName in (".implode(',', $facilityNames).")
				ORDER BY fldSchDate ";
	$result = mysql_query($query);
	if(!$result)
	{
		die('Can not query order details. Debug Query : '.$query);
	}
	if(mysql_num_rows($result) == 0)
	{
		//die('No order details for this contract number '.$contractDetails['number'].' on this month('.$month.') and this year('.$year.')');

		$smarty->assign('noOrderMessage', 'No order details for this contract number '.$contractDetails['number'].' on this month('.$month.') and this year('.$year.')');
		$smarty->assign('contractHasOrder', false);
	}
	else
	{
		$smarty->assign('contractHasOrder', true);
	}

	//init values fee
	$examFees = 0;
	$examFeesDetail = array();
	$examFeesTotal = $procedureCategory;
	$transportFeesDetail = array();
	$examLog = array();

	while($row = mysql_fetch_assoc($result))
	{
		//record fees
		$recordFees = 0;

		//get procedure first
		$procedure = array();
		$description = array();
		for($i = 1; $i < 7 ; $i++)
		{
			if($row['fldProcedure'.$i] != '')
			{
				$procedure[]	= array('exam'		=> $row['fldProcedure'.$i],
										'value'		=> $row['fldplr'.$i]);
			}
		}

		//pass this record if not procedure made, just check for consistent data
		if(count($procedure) == 0) continue;

		$serviceDate			= $row['fldSchDateForLog'];
		$serviceDateFormatted	= $row['fldSchDateFormatted'];
		$facilityName			= $row['fldFacilityName'];
		$stat					= $row['fldStat'];

		if(isset($facilityInfoCache[$facilityName]))
		{
			$rate			= strtoupper($facilityInfoCache[$facilityName]['fldRate']);
			$rateValue		= $facilityInfoCache[$facilityName]['fldRateValue'];
			$zone			= $facilityInfoCache[$facilityName]['fldZone'];
			$rcode			= $facilityInfoCache[$facilityName]['fldRcode'];
			$rateStatValue	= $facilityInfoCache[$facilityName]['fldRateStatValue'];
		}
		else
		{
			die('This Facility <font color="#FF0000">'.$facilityName. '</font> not existed in tblfacility');
		}

		//remove this facility of of array --> this facility have order
		unset($facilityOfContract[$facilityName]);

		//special case, fldPatientID begin with T or A
		$fldPatientID = trim($row['fldPatientID']);
		$firstCharacter = substr($fldPatientID, 0, 1);
		if($firstCharacter == 'T' || $firstCharacter == 'A')
		{
			//0 fees for this record

			$recordFees = 0;

			//add to example log
			foreach($procedure as $item)
			{
				$examLog[] = array(
					'DATE_OF_SERVICE'	=> $serviceDate,
					'PATIENT_NAME'		=> $row['fldFirstName'].' '.$row['fldLastName'],
					'PATIENT_ID'		=> $row['fldPatientID'],
					'EXAM'				=> $item['exam'],
					'VALUE'				=> $item['value'],
					'CBT_CODE'			=> $procedureManagementInfo[$item['exam']]['fldCBTCode'],
					'RATE'				=> number_format(0,2)
				);
			}

		}
		else
		{
			if($rate == 'FLAT')
			{
				//if stat
				if($stat == 1)
					$valueUsed = $rateStatValue;
				else
					$valueUsed = $rateValue;

				$recordFees = roundNumber(count($procedure) * $valueUsed);

				//add to example log
				foreach($procedure as $item)
				{
					$examLog[] = array(
						'DATE_OF_SERVICE'	=> $serviceDate,
						'PATIENT_NAME'		=> $row['fldFirstName'].' '.$row['fldLastName'],
						'PATIENT_ID'		=> $row['fldPatientID'],
						'EXAM'				=> $item['exam'],
						'VALUE'				=> $item['value'],
						'CBT_CODE'			=> $procedureManagementInfo[$item['exam']]['fldCBTCode'],
						'RATE'				=> number_format($valueUsed,2)
					);
				}
			}
			elseif($rate == 'MEDICARE')
			{
				//hmm, think we will get procedure, and and get zone, and get fldz$zoneamount
				$fldzXamount = 'fldz'.$zone.'amount';
				foreach($procedure as $item)
				{
					$exam	= $item['exam'];
					$value	= $item['value'];

					if(!isset($procedureManagementInfo[$exam]))
					{
						die('This procedure <font color="#FF0000">'.$exam.'</font> not existed in tblproceduremagement');
					}

					$itemDetail		= $procedureManagementInfo[$exam];
					$procedurePrice = roundNumber($rateValue * $itemDetail[$fldzXamount]);
					$recordFees		+= $procedurePrice;

					//add to example log
					$examLog[] = array(
						'DATE_OF_SERVICE'	=> $serviceDate,
						'PATIENT_NAME'		=> $row['fldFirstName'].' '.$row['fldLastName'],
						'PATIENT_ID'		=> $row['fldPatientID'],
						'EXAM'				=> $exam,
						'VALUE'				=> $value,
						'CBT_CODE'			=> $itemDetail['fldCBTCode'],
						'RATE'				=> number_format($procedurePrice,2)
					);
				}
			}
		}

		//update EXAM FEES
		$examFees += $recordFees;

		//add count tocategory
		//get this date if set, else make new array with count = 0 for each category
		if(isset($examFeesDetail[$serviceDateFormatted]))
		{
			$temp = $examFeesDetail[$serviceDateFormatted];
		}
		else
			$temp = $procedureCategory;
		//increase category count
		foreach($procedure as $item)
		{
			$exam = $item['exam'];
			$fldCategory	= $procedureManagementInfo[$exam]['fldCategory'];
			if(isset($temp[$fldCategory])) $temp[$fldCategory]++;

			//update total
			$examFeesTotal[$fldCategory]++;
		}
		//set back to array
		$examFeesDetail[$serviceDateFormatted] = $temp;

		//now calculate TRANSPORT FEES
		if(isset($transportFeesDetail[$serviceDateFormatted]))
		{
			$temp = $transportFeesDetail[$serviceDateFormatted];
		}
		else
		{
			//init is count 0, fee = 0
			$temp = array(
					'rCodeCount'	=> 0,
					$facilityName	=> 0,
					'rCode'			=> ''
			);
		}
		if($rcode == 'standard')
		{
			//update the count
			$temp['rCodeCount']++;

			//update rCode
			$temp['rCode'] = RCODE;

			//set fee for this facility, not update, just set
			$temp[$facilityName] = TRANSPORT_FEES;
		}
		//set back to array
		$transportFeesDetail[$serviceDateFormatted] = $temp;

	}

	//recalculate transport fee
	$temp = array();
	$transportFees = 0;
	foreach($transportFeesDetail as $dateOfService => $dataArray)
	{
		//get rCode,rCodeCount and unset
		$rCodeCount = $dataArray['rCodeCount'];
		unset($dataArray['rCodeCount']);
		$rCode = $dataArray['rCode'];
		unset($dataArray['rCode']);

		//get total fee
		$totalFee = 0;
		foreach($dataArray as $facility => $fee)
		{
			$totalFee += $fee;
		}
		$transportFees += $totalFee;

		//add to detail log
		$temp[] = array(
			'DATE_OF_SERVICE'	=> $dateOfService,
			'RCODE'				=> $rCode,
			'AMOUNT_OF_CHARGE'	=> number_format($totalFee,2),
			'TOTAL'				=> number_format($totalFee,2),
		);
	}

	$smarty->assign('examFees', number_format($examFees,2));
	$smarty->assign('procedureCategory', $procedureCategory); //for header
	$smarty->assign('examFeesDetail', $examFeesDetail); //for list of exam fees
	$smarty->assign('examFeesTotal', $examFeesTotal); // for last row

	$smarty->assign('transportFees', number_format($transportFees, 2)); //for transport fees
	$smarty->assign('transportFeesDetail', $temp); //for list of transport fees

	$smarty->assign('totalFees', number_format($examFees + $transportFees, 2));

	$smarty->assign('examLog', $examLog); //for exam log
	$smarty->assign('month', getMonthName($month));
	$smarty->assign('year', $year);
	$smarty->assign('contract', $contractDetails);

	$smarty->assign('examLogNotHaveOrder', $facilityOfContract);

	if(isset($_POST['doVerify']) && $month != $monthServer)
	{
		//insert data first to get record_id
		$amount = $examFees + $transportFees;
		$query = "INSERT INTO billing
					SET contract_id = $contract_id,
						amount = $amount,
						remain_amount = $amount,
						month = $month,
						year = $year,
						date_verified = now(),
						verified_by = {$user->id}";
		mysql_query($query);
		$recordId = mysql_insert_id();
		if(!$recordId)
		{
			$content = 'Can not insert to billing table';
		}
		else
		{
			//we will store this content to db to log
			$smarty->assign('invoiceID', $recordId);
			$content = $smarty->fetch('billing/bill_content.html');
			$log = mysql_real_escape_string($content);

			//update data
			$query = "UPDATE billing
						SET log = '$log',
							invoice_id = '$recordId'
						WHERE id = '$recordId'";
			$update = mysql_query($query);
			if(!$update)
			{
				die('Cannot update data for log and invoice_id field. Please close window and retry');
			}

			//check if need send email
			if(isset($_POST['sendEmail']))
			{
				if($contractDetails['file_format'] == '' || $contractDetails['file_format'] == 'pdf')
				{
					//file name
					$file_name = TEMPORARY_PATH.date('YmdHis').'.pdf';
					exportPDF($content, $file_name);
					$fileForEmail = "Billing_Invoice_{$month}_{$year}.pdf";
				}
				elseif($contractDetails['file_format'] == 'doc')
				{
					//file name
					$file_name = TEMPORARY_PATH.date('YmdHis').'.doc';
					exportDOC($content, $file_name, 'F');
					$fileForEmail = "Billing_Invoice_{$month}_{$year}.doc";
				}
				else
				{
					//file name
					$file_name = TEMPORARY_PATH.date('YmdHis').'.xls';
					exportXLS($content, $file_name, 'F');
					$fileForEmail = "Billing_Invoice_{$month}_{$year}.xls";
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
					$content = '<script type="text/javascript">window.opener.list();</script>
								Bill verified. Send email with billing attach file '.$fileForEmail.' success<br/>';
				}
				else
				{
					//reset content
					$content = '<script type="text/javascript">window.opener.list();</script>
								Bill verified. Cannot send email.<br/>
									Error : '.$check.'<br/>';
				}
			}
			else
			{
				//reset content
				$content = '<script type="text/javascript">window.opener.list();</script>
								Bill verified.<br/>';
			}

			//check if need download - WE EXPORT
			if(isset($_POST['download']))
			{
				$file_name = TEMPORARY_PATH.date('YmdHis').'.pdf';
				exportPDF($content, $file_name);

				//reset content
				$content = '<script type="text/javascript">window.opener.list();</script>
							Bill verified.<br/><a target="_blank" href="'.TEMPORARY_LINK.$file_name.'">Click here to download PDF file</a>';
			}

		}

		$smarty->assign('allowDoVerify', false);
		$smarty->assign('headTitle', 'Process completed');
	}
	else
	{
		$smarty->assign('allowDoVerify', true);
		$smarty->assign('invoiceID', 'will generate when verified');
		$content = $smarty->fetch('billing/bill_content.html');
		$smarty->assign('headTitle', 'Review Billing Detail to Verify');
	}

	//check if need download
	if(isset($_POST['download']))
	{
		$content = $smarty->fetch('billing/bill_content.html');

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

	if($month == $monthServer && $year == $yearServer)
	{
		$smarty->assign('viewForOnGoingBill', true);
		$smarty->assign('headTitle', 'Review Billing Statistics For Current Month');
	}
	else
	{
		$smarty->assign('viewForOnGoingBill', false);
	}

	//show file format
	$smarty->assign('fileFormat', getFileFormatText($contractDetails['file_format']));

	//now display it
	$smarty->assign('content', $content);
	$smarty->display('billing/detail_for_verify.html');