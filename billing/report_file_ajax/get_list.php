<?php
try
{
	require_once '../config.php';

	require_once 'object.php';

	$object  = new BillingReport();
	$results = $object->getList($_GET, $user);

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

	$tempData = array();

	while($row = mysql_fetch_assoc($results))
	{
		$region = $row['region'];
		if($region == '')
		{
			$region = 'NO REGION ASSIGNED';
		}

		if(isset($tempData[$region]))
			$tempData[$region][] = $row;
		else
			$tempData[$region] = array($row);
	}

	$smarty->assign('billRecords', $tempData);
	$content = $smarty->fetch('report/billing_summary.html');

	//check if need download
	if(isset($_GET['download']))
	{
		//file name, if download just the name, not full path
		$file_name = "Billing_Summary_Report_{$_GET['monthSearch']}_{$_GET['yearSearch']}.pdf";

		//header
		$header = 'Billing Summary Report ';

		$content = $object->rawParams.'<br>'.$content;

		exportPDF($content, $file_name, 'D', $header);
		die;
	}

	//check if need downloadexcel
	if(isset($_GET['excel']))
	{
		//file name, if download just the name, not full path
		$file_name = "Billing_Summary_Report_{$_GET['monthSearch']}_{$_GET['yearSearch']}.xls";

		$header = $object->rawParamsExcel;

		exportExcel($tempData, $header, $file_name);
		die;
	}

	$data = '<p align="center">
				<a href="report_file_ajax/get_list.php?'.$object->params.'&download=true" class="downloadPdf"><img align="absmiddle" title="Export to PDF" src="'.PDF_ICON.'"/></a>
				<a href="report_file_ajax/get_list.php?'.$object->params.'&excel=true" class="downloadPdf"><img align="absmiddle" title="Export to Excel" src="'.EXCEL_ICON.'"/></a></p>'.
			$content;

	echo jsonEncode($data);