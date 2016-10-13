<?php

try
{
	require_once '../config.php';
	
	$billing_id		= intval($_GET['billing_id']);
	
	//get bill detail
	$query = "SELECT * FROM billing WHERE id = $billing_id";
	$result = mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Cannot get bill detail. Query :'. $query, false);
		die;
	}
	$billDetails = mysql_fetch_assoc($result);
	
	//get payment
	$query = "SELECT	payment.id,
						payment.billing_id,
						payment.amount,
						payment.check,
						DATE_FORMAT(payment.date,'%m/%d/%Y') AS date_formatted,
						DATE_FORMAT(payment.date_created,'%m/%d/%Y %H:%s') AS date_created_formatted,
						DATE_FORMAT(payment.date_modified,'%m/%d/%Y %H:%s') AS date_modified_formatted,
						userCreate.username AS created_by,
						userModify.username AS modified_by
				FROM payment
					INNER JOIN contract_user AS userCreate
						ON payment.created_by = userCreate.id
					LEFT JOIN contract_user AS userModify
						ON payment.modified_by = userModify.id
					WHERE payment.billing_id = $billing_id
				ORDER by date ";
	//echo $query;die;
	$result = mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Cannot get payments. Query :'. $query, false);
		die;
	}
	if(mysql_num_rows($result) == 0)
	{
		echo jsonEncode('No payment made<hr><b><font color="#0000FF">Remain Amount : $'.$billDetails['remain_amount'].'</font></b>');
		die;
	}
	
	$data = array();
	
	$header_column = '<td>Date</td>
					<td>Amount</td>
					<td>Check Number</td>
					<td>Created By</td>
					<td>Date Created</td>
					<td>Modified By</td>
					<td>Date Modified</td>
					<td width="100" align="center">Action</td>';

	$data[] = '
		<table class="tabledata" cellspacing="0" width="100%">
		<tr class="tabletoprow" align="left">
				'.$header_column.'
		</tr>
		<tr><td colspan="8"><hr></td></tr>
		';
	
	while($row = mysql_fetch_assoc($result))
		{
			$action = '<a href="#" class="editPayment" rel="'.$row['id'].'|'.$row['billing_id'].'">
						<img src="'.EDIT_CON.'" alt="edit" />
					</a>
					<a href="#" class="deletePayment" rel="'.$row['id'].'|'.$row['billing_id'].'">
						<img src="'.DELETE_ICON.'" alt="Delete" />
					</a>';

			 $data[] = '
				<tr>
					<td valign="top">'.$row['date_formatted'].'</td>
					<td valign="top">'.$row['amount'].'</td>
					<td valign="top">'.$row['check'].'</td>
					<td valign="top">'.$row['created_by'].'</td>
					<td valign="top">'.$row['date_created_formatted'].'</td>	
					<td valign="top">'.$row['modified_by'].'</td>
					<td valign="top">'.$row['date_modified_formatted'].'</td>
					<td valign="top" align="center">'.$action.'</td>
				<tr>
				<tr>
					<td valign="top" colspan="8"><hr></td>
				<tr>';
        }
		
		$data[] = '<tr>
					<td valign="top" colspan="8"><b><font color="#0000FF">Remain Amount : $'.$billDetails['remain_amount'].'</font></b></td>
					<tr>';

		$data[] = '</table>';

	echo jsonEncode(implode('',$data));
	

}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getTraceAsString());
	die;
}