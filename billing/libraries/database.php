<?php 
class db{
	
	function db($servername, $dbusername, $dbpassword, $dbname){
		
		return $this->connection($servername, $dbusername, $dbpassword, $dbname);
		
	}
	
	function connection($host, $user, $password, $dbname){
		
		$link = mysql_connect($host,$user,$password) or die ('Cannot connect to the database because: ' . mysql_error());

		mysql_select_db($dbname);
		mysql_query("SET NAMES utf8");
		
		return $link;
	}
	
	function query($strquery,$type=MYSQL_ASSOC){
		$result = mysql_query($strquery);
		$data = array();
		if (mysql_num_rows($result) >= 1){
			while ($line = mysql_fetch_array($result, $type)) {
				$data[] = $line;
			}
			mysql_free_result($result);
		}
		return $data;
	}
	
	
	function query_cell($strquery,$type,$column){
		$result = mysql_query($strquery);
		$data = '';
		if (mysql_num_rows($result) >= 1){
			while ($line = mysql_fetch_array($result, $type)){
				$data = $line[$column];
			}
			mysql_free_result($result);
		}
		return $data;
	}
	
	function query_exist($strquery){
		$result = mysql_query($strquery);
		
		
		if (mysql_num_rows($result) >= 1){
			return true;
		}
		else{
			return false;
		}
		
				
	}
	
	function query_object($strquery){
		$result = mysql_query($strquery);
	
		$object = mysql_fetch_object($result); 
		
		mysql_free_result($result);
		return $object;
	}
	
	function execute($sql){
		mysql_query($sql);
	}
	
}
?>