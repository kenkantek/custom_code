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

				<table id="list2" class="scroll" cellpadding="0" cellspacing="0"></table>
				<div id="pager2" class="scroll" style="text-align:center;"></div>

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

var processFolder	= 'ajax_cars_files/';

grid = $("#list2");

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

jQuery("#list2").jqGrid({
   	url: processFolder + 'get_property_list.php?id=1',
	datatype: function(postdata) {
        jQuery.ajax({
           url: processFolder + 'get_property_list.php?id=1',
           data:postdata,
           dataType:"json",
           complete: function(data,stat){
              if(stat=="success") {
                 var thegrid = jQuery("#list2")[0];
                 thegrid.addJSONData(data);
              }
           }
        });
    },

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
   	pager: jQuery('#pager2'),
	emptyrecords:'No property added',
   	sortname: 'text',
    viewrecords: true,
    sortorder: "asc",
    caption:"Car Properties",
	cellEdit : false,
	cellsubmit : 'remote',
	cellurl : processFolder + 'do_update_property.php?id_car=1',
	editurl : processFolder + 'do_update_property.php?id_car=1',
	ondblClickRow: function(rowid) {
                    jQuery("#list2").jqGrid('editGridRow',rowid,prmEdit);
                }
}).navGrid('#pager2',{add:true,del:true,edit:false}, prmEdit);

</script>
</html>
<?php
	closeConnection();
?>