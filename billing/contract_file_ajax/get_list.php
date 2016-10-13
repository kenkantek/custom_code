<?php
try
{
	require_once '../config.php';
	
	require_once 'object.php';

	$object = new Contract();
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

if($sortColumn == 'start_date')
{
	$header_column = '<td width="80"><a href="#" class="sort" rel="start_date">Start Date</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="email">Email</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td width="100">Action</td>';
}
elseif($sortColumn == 'number')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="email">Email</a></td>	
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td width="100">Action</td>';
}
elseif($sortColumn == 'name')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="email">Email</a></td>	
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td width="100">Action</td>';
}
elseif($sortColumn == 'email')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="email">Email</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td width="100">Action</td>';
}
elseif($sortColumn == 'username')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="email">Email</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a>'.$imageSort.'</td>
					<td width="100">Action</td>';
}

$data = array();

$data[] = '
	<table class="tabledata" cellspacing="0" width="100%">
	<tr class="tabletoprow" align="center">
			'.$header_column.'
	</tr>
	';

		while($row = mysql_fetch_assoc($results))
		{
			if($user->type == ADMIN)
			{
				$action = '<a href="#" class="edit" rel="'.$row['id'].'">
							<img src="'.EDIT_CON.'" alt="edit" />
						</a>
						<a href="#" class="delete" rel="'.$row['id'].'">
							<img src="'.DELETE_ICON.'" alt="Delete" />
						</a>';
			}
			else
			{
				$action = '';
			}
			
			$emails = array();
			if($row['email'] != '') $emails[] = $row['email'];
			if($row['email2'] != '') $emails[] = $row['email2'];
			if($row['email3'] != '') $emails[] = $row['email3'];
			$emails = implode('<br>', $emails);

			 $data[] = '
				<tr>
					<td>'.$row['start_date_formatted'].'</td>
					<td>'.$row['number'].'</td>
					<td>'.$row['name'].'</td>
					<td>'.$emails.'</td>	
					<td>'.$row['username'].'</td>
					<td valign="middle" align="center">
						'.$action.'
					</td>
				</tr>
				<tr>
					<td colspan="6"><hr></td>
				</tr>';
        }

		$data[] = '<tr><td colspan="7" align="center">'.$paging.'</tr>
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