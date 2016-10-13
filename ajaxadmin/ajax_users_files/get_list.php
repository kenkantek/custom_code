<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect database
	connectDatabase();

	require_once 'object.php';


	$object			= new User();
	$result			= $object->getList($_POST);
	$paging         = $object->paging;
	$totalRecord    = $object->totalRecord;
	$range          = $object->range;
    $totalPage      = $object->totalPage;
    $currentPage    = $object->currentPage;

	//close connection
	closeConnection();

	if($result == false)
	{
		echo jsonEncode('No record(s) found', false);
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

	if($sortColumn == 'id' || $sortColumn == 'username')
	{
		$header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company"></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="username">Username'.$imageSort.'</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="type">Type</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}
	elseif($sortColumn == 'type')
	{
		$header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company"></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="username">Username</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="type">Type'.$imageSort.'</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}
	elseif($sortColumn == 'date_created')
	{
		$header_column = $header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company"></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="username">Username</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="type">Type</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created'.$imageSort.'</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}
	elseif($sortColumn == 'status')
	{
		$header_column = $header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company"></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="username">Username</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="type">Type</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status'.$imageSort.'</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}

	$rowData = array();

	$userInfo = getUserInfo();

	while($row = mysql_fetch_assoc($result))
	{
		if($row['status'] == 0)
		{
			$status = ENABLED_ICON;
			$tip	= 'Enabled';

			$rowStatus	= "<a href='#' rel='{$row['id']}|1' alt='Change record status' class='changeStatus'>
								<img src='".DISABLED_ICON."' alt='Do disable record' title='Do disable record' border='0' />
							</a>";
		}
		else
		{
			$status = DISABLED_ICON;
			$tip	= 'Disbaled';

			$rowStatus	= "<a href='#' rel='{$row['id']}|0' alt='Change record status' class='changeStatus'>
								<img src='".ENABLED_ICON."' alt='Do enable record' title='Do enable record' border='0' />
							</a>";
		}

		if(isAdmin())
		{
			if($row['id'] == $userInfo['id'])
			{
				$rowData[] = "<tr>
						<td><img src='".CURRENT_LOGGED_ICON."' alt='You are this user' title='You are this user' border='0' /></td>
						<td>
							<span  class='redText'>{$row['username']}</span>
						</td>
						<td><span  class='redText'>".getUserTypeText($row['type'])."</span></td>
						<td><span  class='redText'>{$row['dateCreated']}</span></td>
						<td><img src='".$status."' alt='{$tip}' title='{$tip}' border='0' /></td>
						<td>
							<a href='#' rel='{$row['id']}' alt='Edit record' class='edit'><img src='".EDIT_ICON."' alt='Edit record' title='Edit record' border='0' /></a>
							{$rowStatus}
						</td>
						</tr>";
			}
			else
			{

				$rowData[] = "<tr>
							<td></td>
							<td>
								{$row['username']}
							</td>
							<td>".getUserTypeText($row['type'])."</td>
							<td>{$row['dateCreated']}</td>
							<td><img src='".$status."' alt='{$tip}' title='{$tip}' border='0' /></td>
							<td>
								<a href='#' rel='{$row['id']}' alt='Edit record' class='edit'><img src='".EDIT_ICON."' alt='Edit record' title='Edit record' border='0' /></a>
								{$rowStatus}
								<a href='#' rel='{$row['id']}' alt='Delete record' class='delete'><img src='".DELETE_ICON."' alt='Delete record' title='Delete record' border='0' /></a>
							</td>
							</tr>";
			}
		}
		else
		{
			if($row['id'] == $userInfo['id'])
			{
				$rowData[] = "<tr>
						<td><img src='".CURRENT_LOGGED_ICON."' alt='You are this user' title='You are this user' border='0' /></td>
						<td>
							<span  class='redText'>{$row['username']}</span>
						</td>
						<td><span  class='redText'>".getUserTypeText($row['type'])."</span></td>
						<td><span  class='redText'>{$row['dateCreated']}</span></td>
						<td><img src='".$status."' alt='{$tip}' title='{$tip}' border='0' /></td>
						<td>
							<a href='#' rel='{$row['id']}' alt='Edit record' class='edit'><img src='".EDIT_ICON."' alt='Edit record' title='Edit record' border='0' /></a>
						</td>
						</tr>";
			}
		}
	}

	$rowData = implode('', $rowData);

	$data = <<<MYFILE

	<table id="rounded-corner" summary="">
					{$header_column}
					<tfoot>
					<tr>
						<td colspan="5" class="rounded-foot-left"><em>Page {$currentPage} of {$totalPage}. Total {$totalRecord} records</em></td>
						<td class="rounded-foot-right">&nbsp;</td>
					</tr>
					</tfoot>
					<tbody>
						$rowData
					</tbody>
					</table>
					<div class="pagination">
						{$paging}
					</div>

MYFILE;

	echo jsonEncode($data);

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}