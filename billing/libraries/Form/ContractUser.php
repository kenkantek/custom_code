<?php
class Form_ContractUser extends FormProcessor{
	function process($request){
		
		global $db;
		
		$this->username		= $this->anti_sql(trim($request['username']));
		$this->password		= $this->anti_sql(trim($request['password']));
		$this->repassword	= $this->anti_sql(trim($request['repassword']));
		$this->type			= $this->anti_sql(trim($request['type']));
		
		if(array_key_exists('id',$request)){
			$this->id = $this->anti_sql((int)($request['id']));
		}else{
			$this->id = 0 ;
			
		}
		
		if ( !$this->username ){
			$this->addError('name_null','Please enter User Name');	
		}
		
		if($this->id==0){
			if ( !$this->password ){
				$this->addError('password_null','Please enter Password');	
			}elseif ($this->password!=$this->repassword){
				$this->addError('repassword_null','Password and Confirm Password are not the same');
			}
			if($db->query_exist("select id from contract_user where username = '$this->username'")){
				$this->addError('username_exists','Username exists');
			}
		}else{
			if ($this->password!=$this->repassword){
				$this->addError('repassword_null','Password and Confirm Password are not the same');
			}
		}

		$this->password = $this->buildPassword($this->password);

		if ($this->id != 0){
			if (!$this->hasError()){
				if($this->password!=""){
					$sql = "UPDATE `contract_user` SET `username` = '$this->username',
							`password` = '$this->password', 
							`type` = '$this->type' WHERE `id`=$this->id";
				}else{
					$sql = "UPDATE `contract_user` SET `username` = '$this->username', 
							`type` = '$this->type' WHERE `id`=$this->id";
				}
				$db->execute($sql);
				return true;

			}

		}
		
		if (!$this->hasError()){
			
			$date_create = date("YmdHis");
		
			if ($this->id == 0){	
				
				 $sql = " INSERT INTO `contract_user` 
				 ( `username`,`password`, `type` )
				   VALUES ( 
					'$this->username', '$this->password','$this->type') ";
					
				$db->execute($sql);
			}else{
				if($this->password!=""){			    	 		
					$sql = "UPDATE `contract_user` SET `username` = '$this->username',
							`password` = '$this->password', 
							`type` = '$this->type' WHERE `id`=$this->id";
				}else{
					$sql = "UPDATE `contract_user` SET `username` = '$this->username', 
							`type` = '$this->type' WHERE `id`=$this->id";
				}							
				$db->execute($sql);
			}
		}
		return $this->hasError();
	}

	function buildPassword($password)
	{
		return sha1($password);
	}

	function checkUser()
	{
		global $db;
		$this->username = $this->anti_sql($this->username);
		$this->password = $this->buildPassword($this->password);
		
		$query = "SELECT * FROM `contract_user` 
					WHERE username='$this->username' AND password = '$this->password'";

		return $db->query_object($query);
	}
}