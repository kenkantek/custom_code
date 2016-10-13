<?php
try
{
	require_once '../config.php';

	global $db;

	$id = intval($_GET['id']);

	$query = "SELECT *
				FROM contract
				WHERE id = '$id'" ;
	$contract = $db->query_object($query);

	if(!$contract)
	{
		echo jsonEncode('Contract not existed');
		die;
	}
	
	$facilityIds = "SELECT facility_id
					FROM contract_facility
					WHERE contract_id = '$id'" ;

	$query = "SELECT fldID AS id,
					fldFacilityName AS name
				FROM tblfacility
				WHERE fldID NOT IN ($facilityIds)
				ORDER BY fldFacilityName ";

	$avaiFacilities = $db->query($query);
	
	$query = "SELECT fldID AS id,
					fldFacilityName AS name
				FROM tblfacility
				WHERE fldID IN ($facilityIds)
				ORDER BY fldFacilityName ";

	$assignedFacilities = $db->query($query);


	$smarty->assign('item', $contract);
	$smarty->assign('userCombo', getUserCombo('assigned_user','comboInForm', $contract->assigned_user));
	$smarty->assign('avaiFacilities', $avaiFacilities);
	$smarty->assign('assignedFacilities', $assignedFacilities);
	
		
	echo jsonEncode($smarty->fetch('contract/form_edit.html'));
}
catch (Exception $e)
{
	echo jsonEncode('Can not connect database', false);
}