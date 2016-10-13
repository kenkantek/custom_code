<?php

require_once '../config.php';

try
{
	//checkLogged
	checkLogged();

	//connect db
	connectDatabase();

	$id		= escapseString($_POST['id']);
	$query	= "SELECT * FROM cars WHERE id = '$id'";
	$result	= mysql_query($query);

	if(!$result || mysql_num_rows($result) == 0)
	{
		//close
		closeConnection();

		echo jsonEncode('Record does not exsited', false);
		die;
	}

	$row = mysql_fetch_assoc($result);

	$categoryCombo	= getCategoryCombo('category', $row['id_category']);

	$statusRadio	= getStatusRadio('status', $row['status']);

	if($row['image'] != '')
	{
		$currentImage	= "<a class='fancybox' href='".IMAGE_URL.$row['image']."'><img width='200' src='".IMAGE_URL.$row['image']."' /></a>";
	}
	else
	{
		$currentImage	= 'No image';
	}

	$currentImage .= '<input type="hidden" value="'.$row['image'].'" id="oldImage" name="oldImage" />';

	//close
	closeConnection();

	$data = <<<MYFILE

		<div class="form">

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
                        <dd><input type="text" name="name" id="name" size="54" value='{$row['name']}' /></dd>
                    </dl>
					<dl>
                        <dt><label for="title">Title</label></dt>
                        <dd><input type="text" name="title" id="title" size="54" value='{$row['title']}' /></dd>
                    </dl>
					<dl>
                        <dt><label for="image">Current Main Image</label></dt>
                        <dd>
							$currentImage
						</dd>
                    </dl>
					<dl>
                        <dt><label for="image">New Main Image</label></dt>
                        <dd><input type="file" name="image" id="image" /> <br/>(jpg/jpge/png/gif only) leave blank to keep current</dd>
                    </dl>
					<dl>
                        <dt><label for="name">Status</label></dt>
                        <dd>
							{$statusRadio}
						</dd>
                    </dl>

					<dl>

					<table id="listProperty" class="scroll" cellpadding="0" cellspacing="0"></table>
					<div id="pagerProperty" class="scroll" style="text-align:center;"></div>
					</dl>

                    <dl>
                        <label for="details">Details</label>
					<dl>
					<dl>
                        <textarea name="details" id="details" rows="15" cols="45">{$row['details']}</textarea>
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