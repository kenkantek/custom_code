<?php
try
{
	require_once '../config.php';
	
	require_once 'object.php';

	$object = new ContractUser();
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
	$imageSort = DOWN_ICON;
}
else
{
	$imageSort = UP_ICON;
}

if($sortColumn == 'username')
{
	$header_column = '<td><a href="#" class="sort" rel="username">Name</a><img align="absmiddle" src="'.$imageSort.'"/></td>
					<td><a href="#" class="sort" rel="type">Type</a></td>
					<td width="100">Action</td>';
}
else
{
	$header_column = '<td><a href="#" class="sort" rel="username">Name</a></td>
					<td><a href="#" class="sort" rel="type">Type</a><img <img align="absmiddle" src="'.$imageSort.'"/></td>
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
			if($user->type == ADMIN && $row['type'] != ADMIN)
			{
				$deleteAction = '
				<a href="#" class="delete" rel="'.$row['id'].'">
                        <img src="'.DELETE_ICON.'" alt="Delete" />
                    </a>';
			}
			else
			{
				$deleteAction = '';
			}
			$type = 'unknow';
			if($row['type'] == ADMIN)
			{
				$type = 'admin';
			}
			elseif($row['type'] == STAFF)
			{
				$type = 'staff';
			}
			elseif($row['type'] == USER)
			{
				$type = 'user';
			}
			elseif($row['type'] == SUPER)
			{
				$type = 'superAdmin';
			}

			 $data[] = '
				<tr>
					<td>'.$row['username'].'</td>
					<td>'.$type.'</td>
					<td valign="middle" align="center">
						<a href="#" class="edit" rel="'.$row['id'].'">
							<img src="'.EDIT_CON.'" alt="edit" />
						</a>
						'.$deleteAction.'
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>';
        }

		$data[] = '<tr><td colspan="7" align="center">'.$paging.'</tr>
		<tr>
            <td colspan="7" align="center">
                Total Records : '.$totalRecord.'
                <br/> View Range : '.$range.'
                <br/> Total Page : '.$totalPage.'
                <br/> Current Page : '.$currentPage.'
            </td>
        </tr>
	</table>';

	echo jsonEncode(implode('',$data));