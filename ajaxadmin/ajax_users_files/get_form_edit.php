<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$id		= escapseString($_POST['id']);
	$query	= "SELECT * FROM users WHERE id = '$id'";
	$result	= mysql_query($query);

	if(!$result || mysql_num_rows($result) == 0)
	{
		//close
		closeConnection();

		echo jsonEncode('Record does not exsited', false);
		die;
	}

	$row = mysql_fetch_assoc($result);

	if(isAdmin())
	{
		$typeCombo		= getUserTypeCombo('type', $row['type']);
		$statusRadio	= getStatusRadio('status', $row['status']);
	}
	else
	{
		$typeCombo		= getUserTypeText($row['type']);
		$statusRadio	= getStatusText($row['status']);
	}

	//close
	closeConnection();

	$data = <<<MYFILE

		<div class="form">

        <form action="#" method="post" id="frmData" class="">

                <fieldset>
					<dl>
                        <dt><label for="gender">User Type:</label></dt>
                        <dd>
                            {$typeCombo}
                        </dd>
                    </dl>
                    <dl>
                        <dt><label for="username">Username</label></dt>
                        <dd>
							{$row['username']}
						</dd>
                    </dl>
					<dl>
                        <dt><label for="password">Password</label></dt>
                        <dd><input type="password" name="password" id="password" size="54" /><br/>(leave blank to keep current password)</dd>
                    </dl>
					<dl>
                        <dt><label for="name">Status</label></dt>
                        <dd>
							{$statusRadio}
						</dd>
                    </dl>

                     <dl class="submit">
						<input type="submit" name="submitButton" id="submitButton" value="Submit" />
						<input type="button" name="cancelButton" id="cancelButton" value="Cancel" />
                     </dl>



                </fieldset>
				<input type='hidden' value='$id' name='id' id='id' />
         </form>

        </div>



MYFILE;

	echo jsonEncode($data);

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}