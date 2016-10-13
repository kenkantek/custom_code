<?php
	include 'header.php';

	connectDatabase();
?>
<body>
<div id="main_container">

	<?php
		include 'header_nav.php';
	?>

    <div class="main_content" style="min-height: 600px;">

		<?php
			include 'main_nav.php';
		?>

		<div class="center_content">



			<div class="left_content">

			<div class="sidebarmenu">

				<a class="menuitem submenuheader" href="">Toolbox</a>
				<div class="submenu">
					<ul>
					<li><a href="#" class="getList">List</a></li>
					<?php
						if(isAdmin())
						{
					?>
					<li><a href="#" class="add">Add</a></li>
					<?php
						}
					?>
					</ul>
				</div>
			</div>


			<div class="sidebar_box" id="searchBox">
				<div class="sidebar_box_top"></div>
				<div class="sidebar_box_content">
				<h3>Search Box</h3>
				<img src="images/info.png" alt="" title="" class="sidebar_icon_right" />
				<form id="frmSearch" class="">
					<p>Type</p>
					<p><?php echo getUserTypeCombo('typeSearch',null,true); ?></p>

					<p>Status</p>
					<p><?php echo getStatusCombo('statusSearch',null,true); ?></p>

					<p>Username</p>
					<p><input type="text" id="usernameSearch" name="usernameSearch" /></p>

					<p><input type="submit" value="Search" /></p>
				</form>
				</div>
				<div class="sidebar_box_bottom"></div>
			</div>


			</div>

			<div class="right_content">

				<h2>Manage Users -> <span id="navTitle">List</span></h2>

				<div id="message" class="warning_box" style="display:none"></div>

				<div id="ajaxLoading" class="loading_box" style="display:none"></div>

				<div id="placeHolder">



				</div>

			</div><!-- end of right content-->



		</div>   <!--end of center content -->




		<div class="clear"></div>
    </div> <!--end of main content-->


    <?php
		include 'footer.php';
	?>

</div>
</body>
<script type="text/javascript">

	var oldForm			= null;
	var processFolder	= 'ajax_users_files/';
	var formCached		= null;
	var currentPage		= 1;
	var sortColumn		= 'id';
	var sortDirection	= 'ASC';

	var listPictureCurrentPage	= 1;

	$(document).ready(function(){
		list();

		$('a.getList').click(function(){
			$('#navTitle').html('List');
			$('#searchBox').show(200);
			return list();
		})

		$('a.add').click(function(){
			$('#navTitle').html('Add');
			$('#searchBox').hide(200);
			return getFormAdd();
		})

		$('#frmSearch').submit(function(){
			formCached	= exportForm('frmSeach');
			currentPage = 1;
			list();

			return false;
		})
	})

	function list()
	{
		$('#searchBox').show(200);

		if(formCached != null)
		{
			importForm('frmSearch', formCached);
		}
		else
		{
			formCached = exportForm('frmSearch');
		}

		$('#placeHolder').hide();

		var form = $('#frmSearch').serializeArray();
		form[form.length] = {name : 'page' , value : currentPage};
		form[form.length] = {name : 'sortColumn' , value : sortColumn};
		form[form.length] = {name : 'sortDirection' , value : sortDirection};

		callAjax('POST','ajaxLoading',
					processFolder + 'get_list.php',
					form,
					beforeSend,
					listSuccess,
					postDataError);

		return false;
	}

	function listSuccess(returnData)
	{
		$('#ajaxLoading').html('').hide();

		if(returnData.result)
		{
			$('#placeHolder').show();
			$('#placeHolder').html(returnData.data);
		}
		else
		{
			displayMessageBox(returnData.data);
		}

		$("a.paging").click(function(){
			currentPage = $(this).attr('rel');
			list();
		})

		$("a.sort").click(function(){
			currentPage = 1;
			if(sortDirection == 'ASC')
				sortDirection = 'DESC';
			else
				sortDirection = 'ASC';
			sortColumn = $(this).attr('rel');

			list();
		})

		$('a.fancybox').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'showNavArrows'	:	false
		});

		$(".delete").jConfirmAction({'yesFunction' : doDelete});

		$(".changeStatus").jConfirmAction({'yesFunction' : doChangeStatus});

		$('a.edit').click(function(){
			$('#navTitle').html('Edit');
			$('#searchBox').hide(200);
			return getFormEdit($(this).attr('rel'));
		})

		$('a.managePicture').click(function(){
			$('#navTitle').html('Manage Picture Gallery');
			$('#searchBox').hide(200);
			return getFormAddPicture($(this).attr('rel'));
		})

	}

	function getFormAdd()
	{
		$('#placeHolder').hide();

		callAjax('POST','ajaxLoading',
					processFolder + 'get_form_add.php',
					'',
					beforeSend,
					getFormAddSucess,
					postDataError);

		return false;
	}

	function getFormAddSucess(returnData)
	{
		$('#ajaxLoading').html('').hide();

		if(returnData.result)
		{
			$('#placeHolder').show();
			$('#placeHolder').html(returnData.data);
		}
		else
		{
			displayMessageBox(returnData.data);
			return false;
		}

		//handle submit
		$('#frmData').submit(function(){
			if($('#username').val() == '')
			{
				displayMessageBox('Please enter Username');
				$('#username').focus();
				return false;
			}
			if($('#password').val() == '')
			{
				displayMessageBox('Please enter Password');
				$('#password').focus();
				return false;
			}

			//callsubmit
			return submitForm();
		})
	}

	function submitForm()
	{
		$('#placeHolder').hide();

		$('#frmData').ajaxSubmit({
					beforeSubmit:	beforeSendForm,
					success:		doAddSucess,
					url:			processFolder + 'do_add.php',
					dataType:		'json',
					error:			postDataError
		});

		return false;
	}

	function doAddSucess(returnData)
	{
		$('#ajaxLoading').html('').hide();

		if(returnData.result)
		{
			displayMessageBox(returnData.data);
			list();
		}
		else
		{
			displayMessageBox(returnData.data);
			$('#placeHolder').show();
		}
	}

	function doDelete(idRecord)
	{
		$('#placeHolder').hide();

		callAjax('POST','ajaxLoading',
			processFolder + 'do_delete.php',
			'id=' + idRecord,
			beforeDelete,
			doDeleteSucess,
			postDataError);

	}

	function doDeleteSucess(returnData)
	{
		if(!returnData.result)
		{
			alert(returnData.data);
		}
		else
		{
			displayMessageBox(returnData.data);
		}
		list();
	}

	function doChangeStatus(param)
	{
		$('#placeHolder').hide();

		callAjax('POST','ajaxLoading',
			processFolder + 'do_change_status.php',
			'param=' + param,
			beforeChangeStatus,
			doChangeStatusSucess,
			postDataError);

	}

	function doChangeStatusSucess(returnData)
	{
		if(!returnData.result)
		{
			alert(returnData.data);
		}
		else
		{
			displayMessageBox(returnData.data);
		}
		list();
	}

	function getFormEdit(idRecord)
	{
		$('#placeHolder').hide();

		callAjax('POST','ajaxLoading',
					processFolder + 'get_form_edit.php',
					'id=' + idRecord,
					beforeSend,
					getFormEditSucess,
					postDataError);

		return false;
	}

	function getFormEditSucess(returnData)
	{
		$('#ajaxLoading').html('').hide();

		if(returnData.result)
		{
			$('#placeHolder').show();
			$('#placeHolder').html(returnData.data);
		}
		else
		{
			displayMessageBox(returnData.data);
			return false;
		}

		//handle submit
		$('#frmData').submit(function(){

			//callsubmit
			return submitFormEdit();
		});

		$('#cancelButton').click(function(){
			list();
		})

	}

	function submitFormEdit()
	{
		$('#placeHolder').hide();

		$('#frmData').ajaxSubmit({
					beforeSubmit:	beforeSendForm,
					success:		doUpdateSucess,
					url:			processFolder + 'do_update.php',
					dataType:		'json',
					error:			postDataError
		});

		return false;
	}

	function doUpdateSucess(returnData)
	{
		$('#ajaxLoading').html('').hide();

		if(returnData.result)
		{
			displayMessageBox(returnData.data);
			list();
		}
		else
		{
			displayMessageBox(returnData.data);
			$('#placeHolder').show();
		}
	}

	function beforeSend()
	{
		$('#ajaxLoading').html('Loading. Please wait...').show();

	}

	function beforeSendForm()
	{
		$('#ajaxLoading').html('Submiting data. Please wait...').show();

	}

	function beforeDelete()
	{
		$('#ajaxLoading').html('Deleteing record. Please wait...').show();

	}

	function beforeChangeStatus()
	{
		$('#ajaxLoading').html('Processing change status of record. Please wait...').show();

	}

	function postDataError(data)
	{
		$('#placeHolder').html('Cannot submit data. Please refresh and retry');
	}
</script>
</html>
<?php
	closeConnection();
?>