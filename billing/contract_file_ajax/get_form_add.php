<?php
require_once '../config.php';

$smarty->assign('userCombo', getUserCombo());

//get file format combo, don't use select all
$smarty->assign('fileFormatCombo', getFileFormatCombo('pdf', 'file_format', '', false));

$smarty->assign('avaiFacilities', getAvailableFacilties());

echo jsonEncode($smarty->fetch('contract/form_add.html'));