<?php

error_reporting(E_ALL);
//ini_set('display_errors',1);

ini_set('memory_limit', -1);
set_time_limit(0);

require_once 'simple_html_dom.php';

if(!isset($_GET['type']) || !isset($_GET['fromPage']) || !isset($_GET['toPage']))
{
	echo ('You must specified type=TYPE&fromPage=X&$toPage=Y<br/>');

	echo '<br/>Example http://bestphpservice.php/scrape/?type=removalists&fromPage=1&toPage=10000';

	echo '<br/>Meaning get removalists , from page 1 to page 10000';

	echo '<br/><br/>if you wist filter by State, add param &state=STATE ';
	echo '<br/>Example http://bestphpservice.php/scrape/?type=removalists&fromPage=1&toPage=10000&state=qld';

	echo '<br/>Meaning get removalists , from page 1 to page 10000, for state QLD';

	die;
}

$type	= strtolower($_GET['type']);
$from	= intval($_GET['fromPage']);
$end	= intval($_GET['toPage']);


if(isset($_GET['state']))
{
	$state	= strtolower($_GET['state']);
	$filename = $type.'_'.$state.'_'.uniqid().'_leads.csv';
}
else
{
	$state = '';
	$filename = $type.'_'.uniqid().'_leads.csv';
}

//write headers
if (!$handle = @fopen($filename, 'w'))
{
	die('Can not open file');
}

fwrite($handle, "Name, Phone, Address\n");


for($page = $from ; $page < $end ; $page++)
{

	if($state != '')
	{
		$url = 'http://www.truelocal.com.au/find/'.$type.'/'.$state.'/'.$page.'/';
	}
	else
	{
		$url = 'http://www.truelocal.com.au/find/'.$type.'/'.$page.'/';
	}

	echo 'Scrape url '.$url;
	echo '<br/>';

	$data = file_get_html($url);

	if($data === false)
	{
		echo 'URL is not existed';
		continue;
	}

	$child = $data->find('div[class=media]');

	$i = 1;
	foreach($child as $item)
	{
		//phone
		foreach($item->find('a[phonenumber]') as $phone)
		{
			$phone = (string)$phone->attr['phonenumber'];
			break;
		}

		//address
		foreach($item->find('p[class=address secondary]') as $address)
		{
			$address = $address->nodes[0];
			$address = $address->_;

			$address = array_pop($address);
			break;
		}

		//name
		//phone
		foreach($item->find('a[class=name]') as $name)
		{
			$name = $name->nodes[0];
			$name = $name->_;

			$name = array_pop($name);
			break;
		}

		if($name != '' && $phone != '' && $address != '')
		{
			$name		= factoryCol($name);
			$phone		= factoryCol($phone);
			$address	= factoryCol($address);

			fwrite($handle, $name.','.$phone.','.$address."\n");
		}

		unset($name);
		unset($phone);
		unset($address);

		$i++;

		if($i > 30)
			break;
	}

}

fclose($handle);


echo '<a href="'.$filename.'">Download file '.$filename.'</a>';

function factoryCol($string){

	$string=str_replace('"','""',$string);

	return "\"{$string}\"";
}

function fWriteData($content,$filename)
{

   if (!$handle = @fopen($filename, 'a')) {
	     $result = 3;//"Cannot open file ($filename)";

    }
    // Write $somecontent to our opened file.
    if (@fwrite($handle, $content) === FALSE) {
        $result = 2;//"Cannot write to file ($filename)";

    }
    else $result = 1;//"Success, wrote ($somecontent) to file ($filename)";
    @fclose($handle);
	return $result;
}

