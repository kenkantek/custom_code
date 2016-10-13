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
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td><a href="#">Months</a></td>';
}
elseif($sortColumn == 'number')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td><a href="#">Months</a></td>';
}
elseif($sortColumn == 'name')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a>'.$imageSort.'</td>
					<td><a href="#" class="sort" rel="username">Assigned User</a></td>
					<td><a href="#">Months</a></td>';
}
elseif($sortColumn == 'username')
{
	$header_column = '<td><a href="#" class="sort" rel="start_date">Start Date</a></td>
					<td><a href="#" class="sort" rel="number">Number</a></td>
					<td><a href="#" class="sort" rel="name">Name</a></td>
					<td><a href="#" class="sort" rel="username">Assigned User</a>'.$imageSort.'</td>
					<td><a href="#">Months</a></td>';
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
			//do query details for this contract_id
			$contract_id = $row['contract_id'];
			

			 $data[] = '
				<tr>
					<td>'.$row['start_date_formatted'].'</td>
					<td>'.$row['number'].'</td>
					<td>'.$row['name'].'</td>
					<td>'.$row['username'].'</td>
					<td>
						12 months with status here
					</td>
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