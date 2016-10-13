<?php
function getUserCombo($name="assigned_user", $class='comboInForm', $selected = null)
{
	global $user;

	$combo	= array();
	$combo[] = "<select name='$name' id='$name' class='$class'>
				<option value=''>[Select]</option>";

	if($user->type != ADMIN && $user->type != STAFF && $user->type != SUPER)
	{
		$combo[]= "<option selected='selected' value='{$user->id}'>{$user->username}</option>";
	}
	else
	{
		$query = "SELECT * FROM contract_user ORDER BY username";
		$result = mysql_query($query);

		while($row = mysql_fetch_assoc($result))
		{
			if($selected != null && $selected == $row['id'])
			{
				$combo[]= "<option selected='selected' value='{$row['id']}'>{$row['username']}</option>";
			}
			else
			{
				$combo[]= "<option value='{$row['id']}'>{$row['username']}</option>";
			}
		}
	}

	$combo[] = '</select>';

	return implode("",$combo);
}

function getAvailableFacilties()
{
	$query = "SELECT tblfacility.fldID AS id,
					fldFacilityName AS name,
					contract_facility.id AS checkId
				FROM tblfacility
					LEFT JOIN contract_facility
						ON tblfacility.fldID = contract_facility.facility_id
				WHERE contract_facility.id is NULL
				ORDER BY fldFacilityName
				";
	global $db;
	$rows = $db->query($query);

	return empty($rows) ? false : $rows;

}

function emptySession()
{
	foreach($_SESSION as $key=>$value)
	{
		unset($_SESSION[$key]);
	}
}

function buildInvoiceID($contractNumber)
{
	return date('Ymd');
}

function exportPDF($content, $file_path, $type = 'F', $header = '', $file_name_for_download = 'Billing.pdf')
{

	require_once('tcpdf/config/lang/eng.php');
	require_once('tcpdf/tcpdf.php');

	// create new PDF document
	$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('SonIT');
	$pdf->SetTitle('Billing Invoice');
	$pdf->SetSubject('Billing');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $header, '');

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 5, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// add a page
	$pdf->AddPage();

	$pdf->SetFont('helvetica', '', 6);

	// -----------------------------------------------------------------------------

	$pdf->writeHTML($content, true, false, false, false, '');


	//Close and output PDF document
	$pdf->Output($file_path, $type, $file_name_for_download);

}

function exportExcel($billRecords, $header, $filename)
{
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("SonIT");
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();

		$rowSub = 2;
		$sheet->mergeCells('A'.$rowSub.':F'.($rowSub+3));
		$sheet->setCellValue('A'.$rowSub, $header);
        $sheet->getStyle('A'.$rowSub)->getFont()->setSize(14);
		$sheet->getStyle('A'.$rowSub)->getFont()->setBold(true);
		$sheet->getStyle('A'.$rowSub)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
		$sheet->getStyle('A'.$rowSub)->getAlignment()->setWrapText(true);
		$sheet->getStyle('A'.$rowSub)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

		$columns = array(
			'A'	=> array(
					'text'	=> 'Paid',
					'field'	=> ''
			),
			'B'	=> array(
					'text'	=> 'Check #',
					'field'	=> ''
			),
			'C'	=> array(
					'text'	=> 'Name',
					'field'	=> ''
			),
			'D'	=> array(
					'text'	=> 'Inv. #',
					'field'	=> '',
			),
			'E'	=> array(
					'text'	=> 'Total',
					'field'	=> ''
			),
			//F for country
			'F'	=> array(
					'text'	=> 'Remaining balance',
					'field'	=> ''
			)
		);

		$borderArray = array('borders' => array(
												'left' => array(
												  'style' => PHPExcel_Style_Border::BORDER_THIN,
												),
												'right' => array(
												  'style' => PHPExcel_Style_Border::BORDER_THIN,
												),
												'top' => array(
												  'style' => PHPExcel_Style_Border::BORDER_THIN,
												),
												'bottom' => array(
												  'style' => PHPExcel_Style_Border::BORDER_THIN
												),
												'inside' => array(
												  'style' => PHPExcel_Style_Border::BORDER_THIN
												)

											)
		);
		$alignArray = array('alignment' => array(
							'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
							'wrap'          => true
		));
		$fontArray	= array('font'	=> array(
				'bold'	=> true
		));

		$alignLeftArray = array('alignment' => array(
							'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
							'wrap'          => true
		));

		$alignRightArray = array('alignment' => array(
							'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
							'wrap'          => true
		));

		//write column header
		$rowSub += 5;

		foreach($billRecords as $region => $rowData)
		{
			$rowSub +=2;
			$sheet->mergeCells('A'.$rowSub.':F'.$rowSub);
			$sheet->setCellValue('A'.$rowSub, $region);
			$sheet->getStyle('A'.$rowSub)->getFont()->setBold(true);
			$sheet->getStyle('A'.$rowSub)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);

			$rowSub++;
			$sheet->getStyle('A'.$rowSub.':F'.$rowSub)->applyFromArray(array_merge($borderArray,$alignArray,$fontArray));
			$sheet->getStyle('A'.$rowSub.':F'.$rowSub)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
			foreach($columns as $column => $arrayItem)
			{
				$sheet->setCellValue($column.$rowSub, $arrayItem['text']);
				$sheet->getColumnDimension($column)->setWidth(25);
			}

			$rowCount = 0;
			$totalCount = 0;
			$remainCount = 0;

			foreach($rowData as $row)
			{
				$rowSub++;
				$sheet->setCellValue('A'.$rowSub, $row['paid']);
				$sheet->setCellValue('B'.$rowSub, $row['check']);
				$sheet->setCellValue('C'.$rowSub, $row['name']);
				if($row['haveNotes'])
				{
					//format red
					$sheet->getStyle('C'.$rowSub)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
				}
				$sheet->setCellValue('D'.$rowSub, $row['invoice_id']);
				$sheet->setCellValue('E'.$rowSub, '$'.number_format($row['amount'],2));
				$sheet->setCellValue('F'.$rowSub, '$'.number_format($row['remain_amount'],2));

				$sheet->getStyle('A'.$rowSub.':F'.$rowSub)->applyFromArray(array_merge($borderArray,$alignArray));

				$sheet->getStyle('C'.$rowSub)->applyFromArray($alignLeftArray);
				$sheet->getStyle('E'.$rowSub)->applyFromArray($alignRightArray);
				$sheet->getStyle('F'.$rowSub)->applyFromArray($alignRightArray);

				$rowCount++;
				$totalCount += $row['amount'];
				$remainCount += $row['remain_amount'];
			}
			$rowSub++;
			$sheet->setCellValue('C'.$rowSub, $rowCount);
			$sheet->setCellValue('E'.$rowSub, '$'.number_format($totalCount,2));
			$sheet->setCellValue('F'.$rowSub, '$'.number_format($remainCount,2));
			$sheet->getStyle('A'.$rowSub.':F'.$rowSub)->applyFromArray(array_merge($borderArray,$alignArray));
			$sheet->getStyle('E'.$rowSub)->applyFromArray($alignRightArray);
			$sheet->getStyle('F'.$rowSub)->applyFromArray($alignRightArray);
		}


		//$sheet->write
		// Redirect output to a client web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
}

function getMonthName($month)
{
	switch($month)
	{
		case '1':
			$name = 'January';
			break;

		case '2':
			$name = 'February';
			break;

		case '3':
			$name = 'March';
			break;

		case '4':
			$name = 'April';
			break;

		case '5':
			$name = 'May';
			break;

		case '6':
			$name = 'June';
			break;

		case '7':
			$name = 'July';
			break;

		case '8':
			$name = 'August';
			break;

		case '9':
			$name = 'September';
			break;

		case '10':
			$name = 'October';
			break;

		case '11':
			$name = 'November';
			break;

		case '12':
			$name = 'December';
			break;

		default:
			$name = 'Month wrong';
			break;
	}

	return $name;
}

function roundNumber($number)
{
	return round($number, 2);
}

function getRegionCombo($name="regionSearch", $class='comboSmall')
{
	global $user;

	$combo	= array();
	$combo[] = "<select name='{$name}[]' class='$class' size='30' multiple='true'>
				";

	if($user->type != ADMIN && $user->type != STAFF && $user->type != SUPER)
	{
		$where = "WHERE assigned_user = '{$user->id}'";
	}
	else
		$where = '';

	$query = "SELECT DISTINCT(region) FROM contract $where";
	$result = mysql_query($query);

	while($row = mysql_fetch_assoc($result))
	{
		$combo[]= "<option value='{$row['region']}'>{$row['region']}</option>";
	}

	$combo[] = '</select>';

	return implode("",$combo);
}

function getFileFormatCombo($selected = null, $name="file_format", $class='comboSmall', $useAll = false)
{
	$items = array(
		'pdf'	=> 'Portable Data Format (.pdf)',
		'doc'	=> 'Microsoft Word 2003 (.doc)',
		'xls'	=> 'Microsoft Excel 2003 (.xls)',
	);

	$combo	= array();
	$combo[] = "<select name='$name' id='$name' class='$class'>";

	if($useAll) $combo[] = "<option value=''>All</option>";

	foreach($items as $ext => $text)
	{
		if($ext == $selected && $selected != null)
			$combo[]= "<option value='{$ext}' selected='selected'>$text</option>";
		else
			$combo[]= "<option value='{$ext}'>$text</option>";
	}

	$combo[] = '</select>';

	return implode("",$combo);
}

function exportDOC($content, $filename, $type = 'D')
{
	if($type == 'D')
	{
		// Redirect output to a client web browser (Word)
		header('Content-Type: application/vnd.ms-word');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		echo $content;
	}
	else
	{
		file_put_contents($filename, $content);
	}
}

function exportXLS($content, $filename, $type = 'D')
{
	if($type == 'D')
	{
		// Redirect output to a client web browser (Excel)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		echo $content;
	}
	else
	{
		file_put_contents($filename, $content);
	}
}

function getFileFormatText($ext)
{
	$items = array(
		'pdf'	=> 'Portable Data Format (.pdf)',
		'doc'	=> 'Microsoft Word 2003 (.doc)',
		'xls'	=> 'Microsoft Excel 2003 (.xls)',
	);

	return $items[$ext];
}