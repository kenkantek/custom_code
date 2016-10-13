//SonIT
//this common function using for call ajax
var ajaxObject		= null;

function callAjax(type, id_div_content,link_action,param, beforeSend, callback_success,callback_error)
{
	//hide messagebox
	hideMessageBox(seconds);

	if(ajaxObject!=null) ajaxObject.abort();

    if (param == null) param = '';
    $.ajax({
						url: link_action,
						type: type,
						data: param,
						dataType: 'json',
						timeout: ajaxTimeOut,
						beforeSend: function(){
								beforeSend();
						},
						error: function(data){
                            if(callback_error!=null) callback_error(data);
                        },
					    success: function(returnData) {
							if(returnData.data == 'TIME_OUT')
							{
								alert('Session Timeout');
								window.location = rootPath + 'logout.php';
								return;
							}
							if(callback_success!=null) callback_success(returnData);
						}
	});
}

var t;
var seconds = 0;

function displayMessageBox(message)
{
	clearTimeout(t);
	$('#message').html(message);
	$('#message').show('fast');
	window.location="#top";
	t = setTimeout("hideMessageBox(seconds)", 3000);
}

function hideMessageBox(seconds)
{
	if (seconds ==0 ) seconds+=3000;
	$('#message').hide(200);
	if (seconds == 3000 ) clearTimeout(t);
}

function checkEmail(emailValue) {
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailValue)){
		return true;
	}
	return false;
}

function changeNavTitle(value)
{
    $('#navTitle').html(value);
}
function fixedEncodeURIComponent (str) {
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28'). replace(/\)/g, '%29').replace(/\*/g, '%2A');
}

function checkDate(ele, text)
{
	var marker="/";
	var submitDate = $('#' + ele).val();
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
		displayMessageBox(text);
		return false;
	}
	for (var i=0; i < 3; i++)
	{
		if(isNaN(dateComp[i]))
		{
			displayMessageBox(text);
			return false;
		}
	}
	if (dateComp[0] > 12 || dateComp[0] < 1)
	{
		displayMessageBox(text);
		return false;
	}
	if(dateComp[2] < 1900)
	{
		displayMessageBox(text);
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
		displayMessageBox(text);
		return false;
	}

	return true;
}

function validateElement(elementArray, messageArray)
{
	for(i = 0 ; i < elementArray.length ; i++)
	{
		if($(elementArray[i]).val() == '')
		{
            $(elementArray[i]).focus();
			displayMessageBox(messageArray[i]);
			return false;
		}
	}

	return true;
}

function checkFileExtension(filename)
{
    var temp = filename.split('.');
    var ext  = temp[temp.length-1].toLowerCase();
    if(ext != 'jpg' && ext != 'jpeg') return false;

    return true;
}

function exportForm(id) {
	var form = document.getElementById(id);
	if (form == null) {
	return null;
	}
	if (typeof(form.elements) == 'undefined') {
	return null;
	}
	var formData = {};
	for (var iterator = 0; iterator < form.elements.length; iterator ++) {
	var element = form.elements[iterator];
	if (element.disabled) {
	continue;
	}
	var elementType = element.tagName.toLowerCase();
	var elementName = null;
	var elementValue = null;
	if (
	(typeof(element.name) != 'undefined') &&
	(element.name.length > 0)) {
	elementName = element.name;
	} else if (
	(typeof(element.id) != 'undefined') &&
	(element.id.length > 0)) {
	elementName = element.id;
	}
	if (elementName != null) {
	if (elementType == 'input') {
	if (
	(element.type == 'text') ||
	(element.type == 'password') ||
	(element.type == 'button') ||
	(element.type == 'submit') ||
	(element.type == 'hidden')) {
	elementValue = element.value;
	} else if (element.type == 'checkbox') {
	elementValue = element.checked;
	} else if (element.type == 'radio') {
	if (element.checked) {
	elementValue = element.value;
	} else {
	try {
	var type = eval('typeof(formData.' + elementName + ')');
	if (type != 'undefined') {
	continue;
	}
	} catch (e) {
	continue;
	}
	}
	}
	} else if (elementType == 'select') {
	if (element.options.length > 0) {
	if (element.multiple) {
	elementName = elementName.replace(/\[\]$/ig, '');
	elementValue = [];
	for (var optionsIterator = 0; optionsIterator < element.options.length; optionsIterator ++) {
	if (element.options[optionsIterator].selected) {
	elementValue.push(element.options[optionsIterator].value);
	}
	}
	} else {
	if (element.selectedIndex >= 0) {
	elementValue = element.options[element.selectedIndex].value;
	}
	}
	}
	} else if (elementType == 'textarea') {
	elementValue = element.value;
	}
	try {
	eval('formData.' + elementName + ' = elementValue;');
	} catch (e) {}
	}
	}
	return formData;
};
function importForm(id, formData) {
	var form = document.getElementById(id);
	if (
	(formData == null) ||
	(form == null)) {
	return false;
	}
	if (typeof(form.elements) == 'undefined') {
	return false;
	}
	for (var iterator = 0; iterator < form.elements.length; iterator ++) {
	var element = form.elements[iterator];
	if (element.disabled) {
	continue;
	}
	var elementType = element.tagName.toLowerCase();
	var elementName = null;
	if (
	(typeof(element.name) != 'undefined') &&
	(element.name.length > 0)) {
	elementName = element.name;
	} else if (
	(typeof(element.id) != 'undefined') &&
	(element.id.length > 0)) {
	elementName = element.id;
	}
	if (elementName != null) {
	if (elementType == 'select') {
	if (element.multiple) {
	elementName = elementName.replace(/\[\]$/ig, '');
	}
	}
	var elementValue = null;
	try {
	var valueType = eval('typeof(formData.' + elementName + ')');
	if (valueType != 'undefined') {
	elementValue = eval('formData.' + elementName);
	} else {
	continue;
	}
	} catch (e) {
	continue;
	}
	if (elementType == 'input') {
	if (
	(element.type == 'text') ||
	(element.type == 'password') ||
	(element.type == 'button') ||
	(element.type == 'submit') ||
	(element.type == 'hidden')) {
	element.value = elementValue;
	} else if (element.type == 'checkbox') {
	element.checked = elementValue;
	} else if (element.type == 'radio') {
	if (element.value == elementValue) {
	element.checked = true;
	} else {
	element.checked = false;
	}
	}
	} else if (elementType == 'select') {
	if (element.options.length > 0) {
	if (element.multiple) {
	element.selectedIndex = -1;
	} else {
	elementValue = [elementValue];
	element.selectedIndex = 0;
	}
	for (var valuesIterator = 0; valuesIterator < elementValue.length; valuesIterator ++) {
	for (var optionsIterator = 0; optionsIterator < element.options.length; optionsIterator ++) {
	if (element.options[optionsIterator].value == elementValue[valuesIterator]) {
	element.options[optionsIterator].selected = true;
	}
	}
	}
	}
	} else if (elementType == 'textarea') {
	element.value = elementValue;
	}
	}
	}
	return true;
};

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};