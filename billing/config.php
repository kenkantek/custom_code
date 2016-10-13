<?php
session_start();

//config for database
$servername	= 'localhost';
$dbusername	= 'root';
$dbpassword	= '';
$dbname		= 'ordernew';


define('BILL_ADDRESS','TECH CARE X-RAY<br>
						3717 CARRINGTON PLACE<br>
						TALLAHASSEE FL 32303<br>');

//define fees
define('TRANSPORT_FEES', 93.44);
define('RCODE', 'R075');

//year begin
define('YEAR_BEGIN', 2011);

//item per page
define('ROW_PER_PAGE', 50);

//how many number show at once
define('WEBSITE_DEFAULT_NUMBER_LINK', 10);

define('DELETE_ICON', 'images/delete.png');
define('EDIT_CON','images/edit.png');
define('UP_ICON', 'images/up.png');
define('DOWN_ICON', 'images/down.png');
define('PDF_ICON', 'images/pdf_icon.png');
define('EXCEL_ICON', 'images/excel_icon.png');

//for display version
define('APPLICATION_VERSION', "1.0");

//define billing path
define("WEBSITE_URL",'http://'.$_SERVER['SERVER_NAME'].'/billing/');
define('WEB_ROOT_PATH','D:/wamp/www/billing/');

define('AJAX_TIMEOUT', 60000);

//path for smarty
define('TEMPLATE_PATH',WEB_ROOT_PATH.'templates');
define('TEMPLATE_COMPILE_PATH',WEB_ROOT_PATH.'template_c');

//path for temp folder
define('TEMPORARY_LINK', WEBSITE_URL.'temp/');
define('TEMPORARY_PATH',WEB_ROOT_PATH.'temp/');

set_include_path(get_include_path().PATH_SEPARATOR.WEB_ROOT_PATH."/libraries/".PATH_SEPARATOR.WEB_ROOT_PATH."/libraries/PHPExcel/");
require_once('libraries/Zend/Loader.php');

Zend_Loader::registerAutoload();
//Smarty
require_once 'libraries/Smarty/Smarty.class.php';
$smarty = new Smarty();
$smarty->debugging      = false;
$smarty->left_delimiter =  '<{';
$smarty->right_delimiter =  '}>';
$smarty->force_compile  = true;
$smarty->caching        = false;
$smarty->compile_check  = true;
$smarty->cache_lifetime = -1;
$smarty->template_dir   = TEMPLATE_PATH;
$smarty->compile_dir    = TEMPLATE_COMPILE_PATH;

require_once 'database.php';

global $db;

$db = new db($servername, $dbusername, $dbpassword, $dbname);

include 'libraries/PagingHelper.php';

function jsonEncode($data, $result =true)
{
    return json_encode(array(
        'result'	=> $result,
        'data'		=> $data
    ));
}

define('ADMIN', '1');
define('USER','0');
define('STAFF','2');
define('SUPER','3');

$user = null;
if(isset($_SESSION['userInfo']))
{
	$smarty->assign('userLogged', true);
	$user = unserialize($_SESSION['userInfo']);
	$smarty->assign('usernameLogged', $user->username);
	$smarty->assign('isAdmin', $user->type == ADMIN);
}

require_once 'common.php';