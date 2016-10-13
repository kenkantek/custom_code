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

		echo jsonEncode('Car does not exsited', false);
		die;
	}

	$row = mysql_fetch_assoc($result);

	$view = "<a target='_blank' href='".SITE_PATH."car-details.php?idCat={$row['id_category']}&id={$row['id']}&name={$row['name']}'>{$row['name']}</a>";

	//close
	closeConnection();

	$data = <<<MYFILE

		<div class="form">

        <form action="#" method="post" id="frmData" class="">

                <table>
                    <tr>
                        <td width="50"><label for="name">Name</label></td>
                        <td>{$view}</td>
                    </tr>
					<tr>
                        <td><label for="title">Title</label></dt>
                        <td>{$row['title']}</td>
                    </tr>

					<tr>
                        <td><label for="image">Add Image</label></td>
                        <td><input type="file" name="image" id="image" /> <br/>(jpg/jpge/png/gif only)</td>
                    </tr>

                     <tr class="submit">
						<td></td>
						<td>
							<input type="submit" name="submitButton" id="submitButton" value="Add" />
						</td>
                     </tr>

                </table>

				<input type='hidden' value='$id' name='id' id='id' />

         </form>

		 <br/><a href="#" class="reloadPictureList">Reload picture list</a><br/><br/><br/>

		 <div id="placeHolderListPicture"></div>

         </div>

MYFILE;

	echo jsonEncode($data);

}
catch (Exception $e)
{
	echo jsonEncode('Exception '.$e, false);
}