<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect database
	connectDatabase();

	$page	= $_GET['page']; // get the requested page
	$limit	= $_GET['rows']; // get how many rows we want to have into the grid
	$sidx	= $_GET['sidx']; // get index row - i.e. user click to sort
	$sord	= $_GET['sord']; // get the direction
	$id_car	= escapseString($_GET['id']);

	if(!$sidx) $sidx =1;


	$result = mysql_query("SELECT COUNT(*) AS count FROM car_properties WHERE id_car = '$id_car'");
	$row    = mysql_fetch_assoc($result);
	$count	= $row['count'];
	if($count == 0)
	{
		die;
	}

	if( $count >0 ) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 0;
	}
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	$SQL = "SELECT * FROM  car_properties WHERE id_car = '$id_car' ORDER BY $sidx $sord LIMIT $start , $limit";
	$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error());

	$response->page = $page;
	$response->total = $total_pages;
	$response->records = $count;
	$i=0;
	while($row = mysql_fetch_assoc($result)) {
		$response->rows[$i]['id']=$row['id'];
		$response->rows[$i]['cell']=array($row['text']);
		$i++;
	}
	echo json_encode($response);
}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}