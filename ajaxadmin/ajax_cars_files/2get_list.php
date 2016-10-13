<?php
try
{
	require_once '../config.php';
	
	require_once 'object.php';

	$object = new Billing();
	$results        = $object->getList($_POST, $user);
	$paging         = $object->paging;
	$totalRecord    = $object->totalRecord;
	$range          = $object->range;
    $totalPage      = $object->totalPage;
    $currentPage    = $object->currentPage;

	if($results == false)
	{
		echo jsonEncode('No record(s) found');
        die;
	}

}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getTraceAsString());
	die;
}

$currentYear = $_POST['currentYear'];

$sortDirection = $object->sortDirection;
$sortColumn	= $object->sortColumn;

if($sortDirection == 'ASC')
{
	$imageSort = '<img align="absmiddle" src="'.DOWN_ICON.'"/>';
}
else
{
	$imageSort = '<img align="absmiddle" src="'.UP_ICON.'"/>';
}

//if($sortColumn == 'start_date')
//{
//	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a>'.$imageSort.'</td>
//					<td><a href="#" class="sort" rel="number">Number</a></td>
//					<td><a href="#" class="sort" rel="name">Name</a></td>
//					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
//					<td width="210"><a href="#">Months</a></td>';
//}
if($sortColumn == 'number')
{
	$header_column = '<td><a href="#" class="sort" rel="number">Contract Number</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="name">Contract Name</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					';
}
elseif($sortColumn == 'name')
{
	$header_column = '<td><a href="#" class="sort" rel="number">Contract Number</a></td>
					<td><a href="#" class="sort" rel="name">Contract Name</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					';
}
elseif($sortColumn == 'username')
{
	$header_column = '<td><a href="#" class="sort" rel="number">Contract Number</a></td>
					<td><a href="#" class="sort" rel="name">Contract Name</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a>'.$imageSort.'</td>
					';
}

$data = array();

$data[] = '
	<table class="tabledata" cellspacing="0" width="100%">
	<tr class="tabletoprow" align="left">
			'.$header_column.'
	</tr>
	<tr><td colspan="3"><hr></td></tr>
	';

		while($row = mysql_fetch_assoc($results))
		{
			//do query details for this contract_id
			$contract_id = $row['id'];
			$query = "SELECT *
						FROM billing
						WHERE year='$currentYear' AND contract_id = '$contract_id'";
			$monthResult = mysql_query($query);
			
			$monthData = array(
				'1'	=> array(
					'title'		=> 'January',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'2'	=> array(
					'title'		=> 'Febuary',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'3'	=> array(
					'title'		=> 'March',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'4'	=> array(
					'title'		=> 'April',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'5'	=> array(
					'title'		=> 'May',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'6'	=> array(
					'title'		=> 'June',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'7'	=> array(
					'title'		=> 'July',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'8'	=> array(
					'title'		=> 'August',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'9'	=> array(
					'title'		=> 'September',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'10'	=> array(
					'title'		=> 'October',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'11'	=> array(
					'title'		=> 'November',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),
				'12'	=> array(
					'title'		=> 'December',
					'amount'	=> 0,
					'remain'	=> 0,
					'status'	=> 'Unverified'
				),

			);
			
			//only show to current month
			$monthServer	= date('n');
			//$monthData = array_slice($monthData, 0, $monthServer);
			
			if(mysql_num_rows($monthResult) > 0);
			{
				while($rowMonth = mysql_fetch_assoc($monthResult))
				{
					$dataMonth = $rowMonth['month'];
					$monthData[$dataMonth]['amount'] = $rowMonth['amount'];
					$monthData[$dataMonth]['remain'] = $rowMonth['remain_amount'];
					$monthData[$dataMonth]['status'] = 'Verified';
					$monthData[$dataMonth]['bill_id'] = $rowMonth['id'];
					$monthData[$dataMonth]['invoice_id'] = $rowMonth['invoice_id'];
				}
			}
			
			$yearServer = date('Y');
			$monthString = array();
			$monthString[] = '<table width="100%"><tr>';
			foreach($monthData as $month => $dataItem)
			{
				//we not show for this year and before month 5
				if($yearServer == YEAR_BEGIN && $month < 5) continue;
					
				if($dataItem['status'] == 'Verified')
				{
					//query count for notes
					$query = "SELECT count(1) AS total
								FROM billing_notes
								WHERE billing_id = {$dataItem['bill_id']}";
					$notesResult = mysql_query($query);
					$notesRow	 = mysql_fetch_assoc($notesResult);
					$notesCount  = $notesRow['total'];
					if($notesCount == 0) $notesCount = '';
					else $notesCount = '('.$notesCount.')';
					
					$monthString[] = '<td valign="top" align="center">'.$dataItem['title'].'<br>
										<a href="#" class="billViewDetail" rel="'.$contract_id.'|'.$month.'|'.$currentYear.'">
											Verified('.$dataItem['invoice_id'].')
										</a>
										<br>
										<a href="#" class="makePayment" rel="'.$contract_id.'|'.$month.'|'.$currentYear.'|'.$dataItem['bill_id'].'">
											$'.$dataItem['amount'].'</a>/$'.$dataItem['remain'].'<br>

										<a href="#" class="makeNote" rel="'.$contract_id.'|'.$month.'|'.$currentYear.'|'.$dataItem['bill_id'].'">
											Notes'.$notesCount.'
											</a>
									</td>';
				}
				elseif($month == $monthServer && $currentYear == $yearServer) //only show Click to Verify if not this month
				{
					$monthString[] = '<td valign="top" align="center">'.$dataItem['title'].'<br>
										<a href="#" class="billViewVerify" rel="'.$contract_id.'|'.$month.'|'.$currentYear.'">
											<b><font color="#0000FF">Preliminary</font></b>
										</a>
									  </td>';
				}
				else
				{
					$monthString[] = '<td valign="top" align="center">'.$dataItem['title'].'<br>
										<a href="#" class="billViewVerify" rel="'.$contract_id.'|'.$month.'|'.$currentYear.'">
											Unverified
										</a>
									 </td>';
				}
			}
			$monthString[] = '</tr></table>';
			$monthString = implode('', $monthString);


			 $data[] = '
				<tr>
					<td valign="top"><b>Contract Number</b></td>
					<td valign="top"><b>Contract Name</b></td>
					<td valign="top"><b>Assigned User</b></td>
				</tr>
				<tr>
					<td valign="top">'.$row['number'].'</td>
					<td valign="top">'.$row['name'].'</td>
					<td valign="top">'.$row['username'].'</td>
				</tr>
				<tr>
					<td valign="top" colspan="3">'.$monthString.'</td>
				<tr>
				<tr>
					<td valign="top" colspan="3"><hr></td>
				<tr>';
        }

		$data[] = '<tr><td colspan="15" align="center">'.$paging.'</tr>
		<tr>
            <td colspan="15" align="center">
                Total Records : '.$totalRecord.'
                <br/> View Range : '.$range.'
                <br/> Total Page : '.$totalPage.'
                <br/> Current Page : '.$currentPage.'
            </td>
        </tr>
	</table>';

	echo jsonEncode(implode('',$data));