<?php
require_once '../config.php';

echo jsonEncode($smarty->fetch('contract_user/form_add.html'));