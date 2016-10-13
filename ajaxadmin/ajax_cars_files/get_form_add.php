<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$categoryCombo	= getCategoryCombo();

	$statusRadio	= getStatusRadio();

	//close
	closeConnection();

	$data = <<<MYFILE

		<div class="form">
				<img src="images/notice.png" class="icon_left" />
				<h4>You can only add property or gallery after car added</h4>

        <form action="#" method="post" id="frmData" class="">

                <fieldset>
					<dl>
                        <dt><label for="gender">Category:</label></dt>
                        <dd>
                            {$categoryCombo}
                        </dd>
                    </dl>
                    <dl>
                        <dt><label for="name">Name</label></dt>
                        <dd><input type="text" name="name" id="name" size="54" /></dd>
                    </dl>
					<dl>
                        <dt><label for="title">Title</label></dt>
                        <dd><input type="text" name="title" id="title" size="54" /></dd>
                    </dl>
					<dl>
                        <dt><label for="image">Main Image</label></dt>
                        <dd><input type="file" name="image" id="image" /> <br/>(jpg/jpge/png/gif only)</dd>
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