//SonIT
//this common function using for call ajax
var ajax_loader_image	= 'images/ajax-loader.gif';
var message_icon		= 'images/message.png';
var ajaxTimeOut			= 600000;

var ajaxReturnData = null;
function callAjax(type, id_div_content,link_action,param,callback_success,callback_error,displayData)
{
    if (param == null) param = '';
    $.ajax({
						url: link_action,
						type: type,
						data: param,
						dataType: 'json',
						timeout: ajaxTimeOut,
						beforeSend: function(){
								$('#'+ id_div_content).html('<img src="' + ajax_loader_image +'" />');
						},
						error: function(data){
                                if (callback_error != null){
                                    eval( callback_error + '();');
                                }
                        },
					    success: function(returnData) {
                                ajaxReturnData = returnData;
                                if(!returnData.result)
                                {
                                    displayMessageBox(returnData.data);
                                }
								if(returnData.data == 'TIME_OUT')
								{
									window.location = 'index.php?m=Session+Timeout';
								}

                                if (displayData || displayData == null)
                                    $('#'+ id_div_content).html(returnData.data);
                                else
                                    displayMessageBox(returnData.data);

                                if (callback_success != null) {
                                    eval( callback_success + '();');
                                }
						}
	});
}

var t;
var seconds = 0;

function displayMessageBox(message)
{
	clearTimeout(t);
	$('#messageDetail').html('<img src="' + message_icon + '" />' + ' ' + message);
	$('#message').show('fast');
	window.location="#top";
	t = setTimeout("hideMessageBox(seconds)",5000);
}

function hideMessageBox(seconds)
{
	if (seconds ==0 ) seconds+=5000;
	$('#message').fadeOut(200);
	if (seconds == 5000 ) clearTimeout(t);
}

function displayImageWaiting()
{
	$('#message').show();
	$('#messageDetail').html('<img src="' + ajax_loader_image +'" />');
}
function displayError()
{
	$('#message').fadeIn('');
	$('#messageDetail').html('Error proccesing data. Press Ctrl + F5 to reload');
    window.location="#top";
}
function displayDeleteOk()
{
	$('#message').addClass('message');
	$('#message').show('fast');
	$('#messageDetail').html('Error proccesing data. Press Ctrl + F5 to reload');
}

function fixedEncodeURIComponent (str) {
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28'). replace(/\)/g, '%29').replace(/\*/g, '%2A');
}

function checkDate(ele, text)
{
	var marker="/";
	var submitDate=document.getElementById(ele).value;
	var dateCompTemp = submitDate.split(marker);
	var dateComp = new Array(3);
	dateComp[0] = dateCompTemp[0];
	dateComp[1] = dateCompTemp[1];
	dateComp[2] = dateCompTemp[2];
	var now = new Date();
	var yearNow=now.getFullYear();
	dayInmonth = new Array(12);
	dayInmonth[0]=31;
	dayInmonth[1]=29;
	dayInmonth[2]=31;
	dayInmonth[3]=30;
	dayInmonth[4]=31;
	dayInmonth[5]=30;
	dayInmonth[6]=31;
	dayInmonth[7]=30;
	dayInmonth[8]=31;
	dayInmonth[9]=31;
	dayInmonth[10]=30;
	dayInmonth[11]=31;
	if (dateComp.length != 3 )
	{
		displayMessageBox("Please enter correct date format for "+text+" (mm/dd/yyyy)!");
		return false;
	}
	for (var i=0; i < 3; i++)
	{
		if(isNaN(dateComp[i]))
		{
			displayMessageBox("Please enter numeric for month, date, and year ( "+text+" )!");
			return false;
		}
	}
	if (dateComp[0] > 12 || dateComp[0] < 1)
	{
		displayMessageBox("Please enter a valid month for "+text+" (1 to 12)!");
		return false;
	}
	if(dateComp[2] < 1900)
	{
		displayMessageBox("Please enter a valid year for "+text+" (from 1900)!");
		return false;
	}

//	if (dateComp[2] > yearNow+1)
//	{
//		displayMessageBox("Please enter a valid year for "+text+"! (future)");
//		return false;
//	}
//	if (dateComp[2] < yearNow-1)
//	{
//		displayMessageBox("Please enter a valid year for "+text+"! (past)");
//		return false;
//	}
	if (dateComp[2] % 4 == 0)
	{
		dayInmonth[1]=29;
	}
	else
	{
		dayInmonth[1]=28;
	}
	if (dateComp[1] > dayInmonth[dateComp[0]-1] || dateComp[1] < 1)
	{
		displayMessageBox("Please enter a valid date!");
		return false;
	}

	return true;
}

function changeNavTitle(value)
{
    $('#navTitle').html(value);
}
function fixedEncodeURIComponent (str) {
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28'). replace(/\)/g, '%29').replace(/\*/g, '%2A');
}

jQuery.fn.sort = function()
{
	return this.pushStack([].sort.apply(this, arguments), []);
};
jQuery.fn.sortOptions = function(sortCallback)
{
	jQuery('option', this)
	.sort(sortCallback)
	.appendTo(this);
	return this;
};
jQuery.fn.sortOptionsByText = function()
{
	var byTextSortCallback = function(x, y)
	{
	var xText = jQuery(x).text().toUpperCase();
	var yText = jQuery(y).text().toUpperCase();
	return (xText < yText) ? -1 : (xText > yText) ? 1 : 0;
	};
	return this.sortOptions(byTextSortCallback);
};
jQuery.fn.sortOptionsByValue = function()
{
	var byValueSortCallback = function(x, y)
	{
		var xVal = jQuery(x).val();
		var yVal = jQuery(y).val();
		return (xVal < yVal) ? -1 : (xVal > yVal) ? 1 : 0;
	};
	return this.sortOptions(byValueSortCallback);
};

function validateEmail(text)
{
	var objRegExp  =/(^[a-z]([a-z_\.]*)@([a-z_\.]*)([.][a-z]{3})$)|(^[a-z]([a-z_\.]*)@([a-z_\.]*)(\.[a-z]{3})(\.[a-z]{2})*$)/i;

	//check for valid email
	return objRegExp.test(text);
}

function MyPopUpWin(url, width, height)
{
		var iMyWidth;
		var iMyHeight;
		//the screen width minus half the new window width (plus 5 pixel borders).
		iMyWidth = (window.screen.width);
		//the screen height minus half the new window height (plus title and status bars).
		iMyHeight = (window.screen.height) - (100 + 50);
		
		if(width == null)
			width = iMyWidth;
		if(height == null)
			height == iMyHeight;
		
		//Open the window.
		return window.open(url, "WindowPopup","status=yes,height=" + iMyHeight +",width=" + iMyWidth + ",resizable=yes,left=auto,top=auto,screenX=auto,screenY=auto,toolbar=no,menubar=no,scrollbars=yes,location=no,directories=no");
}