<?phprequire_once '../config.php';try{	//checkLogged	checkLogged();	//connect db	connectDatabase();	$id			= escapseString($_POST['id']);	if(isAdmin())	{		$type		= escapseString($_POST['type']);		$status		= escapseString($_POST['status']);		if($_POST['password'] != '')		{			$newPassword	= md5($_POST['password']);			$query			= " UPDATE users SET									`type` = '$type',									`status` = '$status',									`password` = '$newPassword',									`date_modified` = now()								WHERE id = '$id'								";		}		else		{			$query			= " UPDATE users SET									`type` = '$type',									`status` = '$status',									`date_modified` = now()								WHERE id = '$id'								";		}		$result = mysql_query($query);		//close		closeConnection();		if($result == false)		{			echo jsonEncode('Cannot update record. Query: '.$query, false);			die;		}		echo jsonEncode('Record was updated success');	}	else	{		//should check logged user id = id record updated ?		//since currently we only allow view itself		//should duplicate :)				if($_POST['passsord'] != '')		{			$newPassword	= md5($_POST['password']);			$query			= " UPDATE users SET									`password` = '$newPassword',									`date_modified` = now()								WHERE id = '$id'								";			$result = mysql_query($query);			//close			closeConnection();			if($result == false)			{				echo jsonEncode('Cannot update record. Query: '.$query, false);				die;			}			echo jsonEncode('Record was updated success');		}		else		{			echo jsonEncode('Nothing changed');		}	}}catch (Exception $e){	echo jsonEncode('Exception '.$e, false);}