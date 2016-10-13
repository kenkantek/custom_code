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
					<li><a href="#" class="add">Add</a></li>
					</ul>
				</div>
			</div>


			<div class="sidebar_box" id="searchBox">
				<div class="sidebar_box_top"></div>
				<div class="sidebar_box_content">
				<h3>Search Box</h3>
				<img src="images/info.png" alt="" title="" class="sidebar_icon_right" />
				<form id="frmSearch" class="">
					<p>Category</p>
					<p><?php echo getCategoryCombo('categorySearch',null,true); ?></p>

					<p>Status</p>
					<p><?php echo getStatusCombo('statusSearch',null,true); ?></p>

					<p>Name</p>
					<p><input type="text" id="nameSearch" name="nameSearch" /></p>

					<p><input type="submit" value="Search" /></p>
				</form>
				</div>
				<div class="sidebar_box_bottom"></div>
			</div>


			</div>

			<div class="right_content">

				<h2>Manage Cars -> <span id="navTitle">List</span></h2>

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
	var processFolder	= 'ajax_cars_files/';
	var formCached		= null;
	var currentPage		= 1;
	var sortColumn		= 'id';
	var sortDirection	= 'ASC';

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

		//handle other at here
//		existingLoadEvent();
//		NFInit();

		var fckConfigFile		= rootPath + 'myfckconfig.js';
		var oFCKeditorContentEn = new FCKeditor('details');
		oFCKeditorContentEn.BasePath = "fckeditor/" ;
		oFCKeditorContentEn.Height = 600;
		oFCKeditorContentEn.Config["CustomConfigurationsPath"] = fckConfigFile  ;
		oFCKeditorContentEn.ReplaceTextarea();

		//handle submit
		$('#frmData').submit(function(){
			if($('#name').val() == '')
			{
				displayMessageBox('Please enter Name');
				$('#name').focus();
				return false;
			}
			if($('#title').val() == '')
			{
				displayMessageBox('Please enter Title');
				$('#title').focus();
				return false;
			}

			//must get all content from FCK first, else LOST DATA
			var oEditor = FCKeditorAPI.GetInstance('details') ;
			$('#details').val(oEditor.GetHTML());

			if($('#details').val() == '')
			{
				displayMessageBox('Please enter Details');
				$('#details').focus();
				return false;
			}

			if($('#image').val() == '')
			{
				displayMessageBox('Please select Main image');
				$('#image').focus();
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

		//handle other at here
		//existingLoadEvent();
		//NFInit();

		var fckConfigFile		= rootPath + 'myfckconfig.js';
		var oFCKeditorContentEn = new FCKeditor('details');
		oFCKeditorContentEn.BasePath = "fckeditor/" ;
		oFCKeditorContentEn.Height = 600;
		oFCKeditorContentEn.Config["CustomConfigurationsPath"] = fckConfigFile  ;
		oFCKeditorContentEn.ReplaceTextarea();

		$('a.fancybox').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'showNavArrows'	:	false
		});

		//handle submit
		$('#frmData').submit(function(){
			if($('#name').val() == '')
			{
				displayMessageBox('Please enter Name');
				$('#name').focus();
				return false;
			}

			if($('#title').val() == '')
			{
				displayMessageBox('Please enter Title');
				$('#title').focus();
				return false;
			}

			//must get all content from FCK first, else LOST DATA
			var oEditor = FCKeditorAPI.GetInstance('details') ;
			$('#details').val(oEditor.GetHTML());

			if($('#details').val() == '')
			{
				displayMessageBox('Please enter Details');
				$('#details').focus();
				return false;
			}

//			if($('#image').val() == '')
//			{
//				displayMessageBox('Please select Main image');
//				$('#image').focus();
//				return false;
//			}

			//callsubmit
			return submitFormEdit();
		});


		var grid = $("#listProperty");

		prmEdit = {
                    errorTextFormat: function(data) {
                        var myInfo = '<div class="ui-state-highlight ui-corner-all">'+
                                     '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>' +
                                     '<strong>My information text:</strong><br/>' +
                                     '</div>',
                            infoTR = $("table#TblGrid_"+grid[0].id+">tbody>tr.tinfo"),
                            infoTD = infoTR.children("td.topinfo");
							infoTD.html(myInfo);
							infoTR.show();

							setTimeout(function() {
								infoTD.children("div").fadeOut('slow',
									function() {
									  // Animation complete.
									  infoTR.hide();
								 });
							},3000);

                        return '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+
                               "<strong>Error information:<strong></br>Status: '" +
                                 data.statusText + "'. Error code: " + data.status;
                    },
					afterSubmit: function(data) {
						var dataJson = jQuery.parseJSON(data.responseText);

						var myInfo = '<div class="ui-state-highlight ui-corner-all">'+
                                     '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>' +
                                     '<strong>Result:</strong><br/>' +  dataJson.data + '</div>',
                            infoTR = $("table#TblGrid_"+grid[0].id+">tbody>tr.tinfo"),
                            infoTD = infoTR.children("td.topinfo");
							infoTD.html(myInfo);
							infoTR.show();

							setTimeout(function() {
								infoTD.children("div").fadeOut('slow',
									function() {
									  // Animation complete.
									  infoTR.hide();
								 });
							},3000);

                        return '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+
                               "<strong>" + dataJson.data + "</strong>";
					}
                };

		jQuery("#listProperty").jqGrid({
			url: processFolder + 'get_property_list.php?id=' + $('#id').val(),
			datatype: "json",
			colNames:['Text'],
			colModel:[
				{name:'text',index:'text', editable:true, edittype:'text',
					editoptions: {size:100, maxlength: 1000},
					editrules:{required:true}
				},
			],
			autowidth:true,
			rowNum:20,
			rowList:[20,40,60],
			imgpath: 'images/',
			pager: jQuery('#pagerProperty'),
			emptyrecords:'No property added',
			sortname: 'text',
			viewrecords: true,
			sortorder: "asc",
			caption:"Car Properties",
			cellEdit : false,
			cellsubmit : 'remote',
			cellurl : processFolder + 'do_update_property.php?id_car=' + $('#id').val(),
			editurl : processFolder + 'do_update_property.php?id_car=' + $('#id').val(),
			ondblClickRow: function(rowid) {
							jQuery("#listProperty").jqGrid('editGridRow',rowid,prmEdit);
						}
		}).navGrid('#pagerProperty',{add:true,del:true,edit:false}, prmEdit);
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