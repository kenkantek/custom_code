<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect database
	connectDatabase();

	require_once 'object.php';


	$object			= new Cars();
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

	if($sortColumn == 'id' || $sortColumn == 'name')
	{
		$header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company">Image</th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="name">Name'.$imageSort.'</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="category.name">Category</a></th>
							<th scope="col" class="rounded"><a href="#" class="" rel="">Gallery</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}
	elseif($sortColumn == 'category.name')
	{
		$header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company">Image</th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="name">Name</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="category.name">Category'.$imageSort.'</a></th>
							<th scope="col" class="rounded"><a href="#" class="" rel="">Gallery</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}
	elseif($sortColumn == 'date_created')
	{
		$header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company">Image</th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="name">Name</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="category.name">Category</a></th>
							<th scope="col" class="rounded"><a href="#" class="" rel="">Gallery</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created'.$imageSort.'</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}
	elseif($sortColumn == 'status')
	{
		$header_column = '<thead>
							<tr>
							<th scope="col" class="rounded-company">Image</th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="name">Name</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort"rel="category.name">Category</a></th>
							<th scope="col" class="rounded"><a href="#" class="" rel="">Gallery</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="date_created">Date Created</a></th>
							<th scope="col" class="rounded"><a href="#" class="sort" rel="status">Status'.$imageSort.'</a></th>
							<th scope="col" class="rounded-q4" width="10">Action</th>
							</tr>
							</thead>
						';
	}

	$rowData = array();

	while($row = mysql_fetch_assoc($result))
	{
		if($row['status'] == 0)
		{
			$status = ENABLED_ICON;
			$tip	= 'Enabled';

			$rowStatus	= "<a href='#' rel='{$row['id']}|1' alt='Change record status' class='changeStatus'>
								<img src='".DISABLED_ICON."' alt='Do disable record' title='Do disable record' border='0' />
							</a>";
			$view		= "<a target='_blank' href='".SITE_PATH."car-details.php?idCat={$row['id_category']}&id={$row['id']}&name={$row['name']}'>{$row['name']}</a>";
		}
		else
		{
			$status = DISABLED_ICON;
			$tip	= 'Disbaled';

			$rowStatus	= "<a href='#' rel='{$row['id']}|0' alt='Change record status' class='changeStatus'>
								<img src='".ENABLED_ICON."' alt='Do enable record' title='Do enable record' border='0' />
							</a>";
			$view		= $row['name'];
		}

		$rowData[] = "<tr>
					<td>
						<a class='fancybox' href='".IMAGE_URL.$row['image']."'>
							<img src='".IMAGE_URL.$row['image']."' width='80' />
						</a>
					</td>
					<td>{$view}</td>
					<td>{$row['category_name']}</td>
					<td>
						<a href='#' rel='{$row['id']}' alt='Manage Picture' class='managePicture'>
							{$row['totalImage']} picture(s)
						</a>
					</td>
					<td>{$row['dateCreated']}</td>
					<td><img src='".$status."' alt='{$tip}' title='{$tip}' border='0' /></td>
					<td>
						<a href='#' rel='{$row['id']}' alt='Edit record' class='edit'><img src='".EDIT_ICON."' alt='Edit record' title='Edit record' border='0' /></a>
						{$rowStatus}
						<a href='#' rel='{$row['id']}' alt='Delete record' class='delete'><img src='".DELETE_ICON."' alt='Delete record' title='Delete record' border='0' /></a>
					</td>
					</tr>";
	}

	$rowData = implode('', $rowData);

	$data = <<<MYFILE

	<table id="rounded-corner" summary="">
					{$header_column}
					<tfoot>
					<tr>
						<td colspan="6" class="rounded-foot-left"><em>Page {$currentPage} of {$totalPage}. Total {$totalRecord} records</em></td>
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

	//<span class="disabled"><< prev</span><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
	echo jsonEncode($data);

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}