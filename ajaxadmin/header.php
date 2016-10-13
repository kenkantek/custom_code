<?php
	require_once 'config.php';

	checkLogged(false);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ADMIN PANEL</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/ddaccordion.js"></script>
<script type="text/javascript">
ddaccordion.init({
	headerclass: "submenuheader", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["suffix", "<img src='images/plus.gif' class='statusicon' />", "<img src='images/minus.gif' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})
</script>
<script type="text/javascript" src="js/jquery.jclock-1.2.0.js"></script>
<script type="text/javascript" src="js/jconfirmaction.jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		//$('.ask').jConfirmAction();

		$('.jclock').jclock();
	});

	document.write('<div style="display:none">\n\
						<img src="images/warning.png" />\n\
						<img src="images/error.png" />\n\
						<img src="images/valid.png" />\n\
						<img src="images/ajax-loader.gif" />\n\
						<img src="images/up.png" />\n\
						<img src="images/down.png" />\n\
						<img src="images/bubble.png" />\n\
						<img src="images/buttony.png" />\n\
						<img src="images/buttonn.png" />\n\
						<img src="images/enabled.png" />\n\
						<img src="images/disabled.png" />\n\
					</div>');

	var rootPath			= '<?php echo ROOT_PATH; ?>';
	var ajax_loader_image	= 'images/ajax-loader.gif';
	var ajaxTimeOut			= '<?php echo AJAX_TIME_OUT; ?>';
</script>
<script type="text/javascript" src="js/niceforms.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="niceforms-default.css" />
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="js/fancybox/jquery.fancybox-1.3.4.css" />

<script src="fckeditor/fckeditor.js" type="text/javascript"></script>

<!--<script language="javascript" type="text/javascript" src="js/flexigrid/js/flexigrid.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="js/flexigrid/css/flexigrid.css" />-->

<link rel="stylesheet" type="text/css" media="screen" href="js/redmond/jquery-ui-1.8.6.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="js/jqGrid/ui.jqgrid.css" />

<script type="text/javascript" src="js/jqGrid/grid.locale-en.js" ></script>
<script type="text/javascript" src="js/jqGrid/jquery.jqGrid.min.js" ></script>
</head>