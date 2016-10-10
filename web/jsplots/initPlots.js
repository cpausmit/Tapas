function initPlots()
{
    var ajaxInput = {
	'url': '/assignmentsPerSemester',
	'data': { 'key1': 'value1', 'key2': 'value2' }, // JSON: 'key': value. Entries -> $_REQUEST['key'] (in php)
	'success': function (data, textStatus, jqXHR) { makeTapasPlots(data); }, // data contains output from PHP
	'dataType': 'json',
	'async': false
    };
    $.ajax(ajaxInput);
};
