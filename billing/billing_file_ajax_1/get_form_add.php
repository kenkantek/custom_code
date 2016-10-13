<?php
require_once '../config.php';

$smarty->assign('userCombo', getUserCombo());

$smarty->assign('avaiFacilities', getAvailableFacilties());

echo jsonEncode($smarty->fetch('contract/form_add.html'));