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
	
	//get note
	$query = "SELECT	billing_notes.id,
						billing_notes.billing_id,
						billing_notes.notes,
						DATE_FORMAT(billing_notes.date,'%m/%d/%Y') AS date_formatted,
						DATE_FORMAT(billing_notes.date_created,'%m/%d/%Y %H:%s') AS date_created_formatted,
						DATE_FORMAT(billing_notes.date_modified,'%m/%d/%Y %H:%s') AS date_modified_formatted,
						userCreate.username AS created_by,
						userModify.username AS modified_by
				FROM billing_notes
					INNER JOIN contract_user AS userCreate
						ON billing_notes.created_by = userCreate.id
					LEFT JOIN contract_user AS userModify
						ON billing_notes.modified_by = userModify.id
					WHERE billing_notes.billing_id = $billing_id
				ORDER by date ";
	//echo $query;die;
	$result = mysql_query($query);
	if(!$result)
	{
		echo jsonEncode('Cannot get notes. Query :'. $query, false);
		die;
	}
	if(mysql_num_rows($result) == 0)
	{
		echo jsonEncode('No Notes');
		die;
	}
	
	$data = array();
	
	$header_column = '<td><b>Date</b></td>
					<td><b>Notes</b></td>
					<td><b>Created By</b></td>
					<td><b>Date Created</b></td>
					<td><b>Modified By</b></td>
					<td><b>Date Modified</b></td>
					<td width="100" align="center"><b>Action</b></td>';

	$data[] = '
		<table class="tabledata" cellspacing="0" width="100%">
		<tr class="tabletoprow" align="left">
				'.$header_column.'
		</tr>
		<tr><td colspan="8"><hr></td></tr>
		';
	
	while($row = mysql_fetch_assoc($result))
		{
			$action = '<a href="#" class="editNote" rel="'.$row['id'].'|'.$row['billing_id'].'">
						<img src="'.EDIT_CON.'" alt="edit" />
					</a>
					';

			if($user->type == ADMIN)
			{
				$action .= '<a href="#" class="deleteNote" rel="'.$row['id'].'|'.$row['billing_id'].'">
							<img src="'.DELETE_ICON.'" alt="Delete" />
						</a>';
			}

			 $data[] = '
				<tr>
					<td valign="top">'.$row['date_formatted'].'</td>
					<td valign="top">'.$row['notes'].'</td>
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
		
		$data[] = '</table>';

	echo jsonEncode(implode('',$data));
	

}
catch(Exception $e)
{
	echo jsonEncode('Error : '.$e->getTraceAsString());
	die;
}