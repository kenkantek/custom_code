<?php

session_start();

define('SITE_PATH', 'http://'.$_SERVER['HTTP_HOST'].'/');

define('AJAX_TIME_OUT', 6000);

define('ROOT_PATH', 'http://'.$_SERVER['HTTP_HOST'].'/');

//define for up/down image
define('DOWN_ICON'		, 'images/down.png');
define('UP_ICON'		, 'images/up.png');
define('EDIT_ICON'		, 'images/user_edit.png');
define('DELETE_ICON'	, 'images/trash.png');
define('ENABLED_ICON'	, 'images/enabled.png');
define('DISABLED_ICON'	, 'images/disabled.png');
define('CURRENT_LOGGED_ICON'	, 'images/flag.png');

require_once 'libraries/PagingHelper.php';
require_once 'libraries/FileHelper.php';

//common functions
function jsonEncode($data, $result = true)
{
	return json_encode(array(
		'data'		=> $data,
		'result'	=> $result
	));
}

function checkLogged($is_ajax = true)
{
	if(!isset($_SESSION['userInfo']))
	{
		if($is_ajax)
		{
			echo jsonEncode('TIME_OUT');
			die;
		}
		else
		{
			header("Location: index.php");
			die;
		}
	}
}

function getUserInfo()
{
	if(is_array($_SESSION['userInfo'])) return $_SESSION['userInfo'];

	return unserialize($_SESSION['userInfo']);
}

function connectDatabase($is_ajax = true)
{
	$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$connection)
	{
		if($is_ajax)
		{
			echo jsonEncode('Cannot connect to database server', false);
			die;
		}
		else
		{
			die('Cannot connect to database server');
		}
	}

	$selectdb = mysql_select_db(DB_DATABASE);
	if(!$selectdb)
	{
		if($is_ajax)
		{
			echo jsonEncode('Cannot select database', false);
			die;
		}
		else
		{
			die('Cannot select database');
		}
	}
}

function closeConnection()
{
	mysql_close();
}

function escapseString($string)
{
	if(get_magic_quotes_gpc()) return $string;

	return mysql_real_escape_string($string);
}

function getStatusCombo($name = 'status', $select = null, $useAll = false)
{
	$combo = array();
	$combo[] = "<select name='$name' id='$name'>";

	if($useAll)
		$combo[] = "<option value=''>All</option>";

	$item = array(
		'0'	=> 'Enabled',
		'1'	=> 'Disabled'
	);

	foreach($item as $value => $text)
	{
		if($select != null  && $select == $value)
		{
			$combo[] = "<option value='$value' selected='selected'>$text</option>";
		}
		else
		{
			$combo[] = "<option value='$value' >$text</option>";
		}
	}

	$combo[] = "</select>";

	return implode('', $combo);
}

function getCategoryCombo($name = 'category', $select = null, $useAll = false)
{
	$combo = array();
	$combo[] = "<select name='$name' id='$name'>";

	if($useAll)
		$combo[] = "<option value=''>All</option>";

	$query	= "SELECT * FROM category";
	$result	= mysql_query($query);

	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{
			if($select != null  && $select == $row['id'])
			{
				$combo[] = "<option value='{$row['id']}' selected='selected'>{$row['name']}</option>";
			}
			else
			{
				$combo[] = "<option value='{$row['id']}' >{$row['name']}</option>";
			}
		}
	}

	$combo[] = "</select>";

	return implode('', $combo);
}

function getStatusRadio($name = 'status', $select = '0')
{
	$combo = array();

	$item = array(
		'0'	=> 'Enabled',
		'1'	=> 'Disabled'
	);

	foreach($item as $value => $text)
	{
		if($select != null  && $select == $value)
		{
			$combo[] = "<input type='radio' name='$name' value='$value' checked='checked'>$text";
		}
		else
		{
			$combo[] = "<input type='radio' name='$name' value='$value'>$text";
		}
	}

	return implode('', $combo);
}

function getStatusText($value)
{
	$item = array(
		'0'	=> 'Enabled',
		'1'	=> 'Disabled'
	);

	return $item[$value];
}

function getUserTypeCombo($name = 'type', $select = null, $useAll = false)
{
	$combo = array();
	$combo[] = "<select name='$name' id='$name'>";

	$userInfo = getUserInfo();

	//admin
	if($userInfo['type'] == '1')
	{

		$type = array(
			'0'	=> 'User',
			'1'	=> 'Admin'
		);

		if($useAll)
			$combo[] = "<option value=''>All</option>";

		foreach($type as $value => $text)
		{
			if($select != null  && $select == $value)
			{
				$combo[] = "<option value='{$value}' selected='selected'>{$text}</option>";
			}
			else
			{
				$combo[] = "<option value='{$value}' >{$text}</option>";
			}
		}

		$combo[] = "</select>";
	}
	else
	{
		$type = array(
			'0'	=> 'User'
		);

		foreach($type as $value => $text)
		{
			if($select != null  && $select == $value)
			{
				$combo[] = "<option value='{$value}' selected='selected'>{$text}</option>";
			}
			else
			{
				$combo[] = "<option value='{$value}' >{$text}</option>";
			}
		}

		$combo[] = "</select>";
	}

	return implode('', $combo);
}

function getUserTypeText($value)
{
	$type = array(
			'1'	=> 'Admin',
			'0'	=> 'User'
	);

	return $type[$value];
}

function isAdmin()
{
	$userInfo = getUserInfo();

	return ($userInfo['type'] == '1' ? true : false);
}

function isDuplicateUser($username)
{
	$query	= "SELECT id FROM users WHERE username = '$username'";
	$result	= mysql_query($query);
	return (mysql_num_rows($result) == 0 ? false : true);
}