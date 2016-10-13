<?php

//put database config at here
define('DB_HOST',		'projectandmorecom.ipagemysql.com');
define('DB_USER',		'ajaxadmin');
define('DB_PASSWORD',	'987123');
define('DB_DATABASE',	'ajaxadmin');


//subfolder name for ADMIN
define('ADMIN_FOLDER',	'administrator');

//define for paging
define('ROW_PER_PAGE', 20);
define('WEBSITE_DEFAULT_NUMBER_LINK', 10);

//define path for main image
define('IMAGE_PATH',		'/home/users/web/b674/ipg.projectandmorecom/ajaxadmin/car_images/main/');
define('IMAGE_URL',			'http://ajaxadmin.projectandmore.com/car_images/main/');
define('IMAGE_WIDTH',		500);
define('IMAGE_HEIGHT',		400);

//define path for gallery images
define('GALLERY_IMAGE_PATH',		'/home/users/web/b674/ipg.projectandmorecom/ajaxadmin/car_images/gallery/');
define('GALLERY_IMAGE_URL',			'http://ajaxadmin.projectandmore.com/car_images/gallery/');
define('GALLERY_THUMB_IMAGE_PATH',	GALLERY_IMAGE_PATH.'thumb/');
define('GALLERY_THUMB_IMAGE_URL',	GALLERY_IMAGE_URL.'/thumb/');



//do not edit below lines------------------------------------
require_once 'common_functions.php';