<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	if(!isAdmin())
	{
		echo jsonEncode('Permission denied. Only admin can add user', false);
		die;
	}

	//connect db
	connectDatabase();

	$typeCombo		= getUserTypeCombo();

	$statusRadio	= getStatusRadio();

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
                        <dd><input type="text" name="username" id="username" size="54" /></dd>
                    </dl>
					<dl>
                        <dt><label for="password">Password</label></dt>
                        <dd><input type="password" name="password" id="password" size="54" /></dd>
                    </dl>
					<dl>
                        <dt><label for="name">Status</label></dt>
                        <dd>
							{$statusRadio}
						</dd>
                    </dl>

                     <dl class="submit">
                    <input type="submit" name="submitButton" id="submitButton" value="Submit" />
                     </dl>



                </fieldset>

         </form>
         </div>

MYFILE;

	echo jsonEncode($data);

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}