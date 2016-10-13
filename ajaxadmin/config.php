<?php

//put database config at here
define('DB_HOST',		'projectandmorecom.ipagemysql.com');
define('DB_USER',		'ajaxadmin');
define('DB_PASSWORD',	'987123');
define('DB_DATABASE',	'ajaxadmin');


//subfolder name for ADMIN
define('ADMIN_FOLDER',	'ajaxadmin');

//define for paging
define('ROW_PER_PAGE', 10);
define('WEBSITE_DEFAULT_NUMBER_LINK', 10);

//define path for main image
define('IMAGE_PATH',		'/home/users/web/b674/ipg.projectandmorecom/ajaxadmin/car_images/main/');
define('IMAGE_URL',			'http://ajaxadmin.projectandmore.com/car_images/main/');
define('IMAGE_WIDTH',		500);
define('IMAGE_HEIGHT',		400);

//define path for gallery images
define('GALLERY_IMAGE_PATH',		'/home/users/web/b674/ipg.projectandmorecom/ajaxadmin/car_images/gallery/');
define('GALLERY_IMAGE_URL',			'http://ajaxadmin.projectandmore.com/car_images/gallery/');
//size for gallery, which will auto resized
define('GALLERY_IMAGE_WIDTH', 860);
define('GALLERY_IMAGE_HEIGHT', 640);
define('GALLERY_THUMB_IMAGE_WIDTH',		200); //gallery thumb width
define('GALLERY_THUMB_IMAGE_HEIGHT',		149); //gallery thumb height

//ALL IMAGE SIZE
define('IMAGE_MAX_SIZE', 2);//2M


//do not edit below lines------------------------------------
require_once 'common_functions.php';