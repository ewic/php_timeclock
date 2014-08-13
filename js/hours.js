function delete_hours_check() {
	makeRequest('index.php',1,"delete_hours_check",display_result,delete_hours_check.arguments);
}
function delete_hours() {
	makeRequest('index.php',1,"delete_hours",hours_result,delete_hours.arguments);
}

function record() {
	var args = new Array();
	args[0] = document.forms.record_hours.work_id.value; 
	args[1] = document.forms.record_hours.hours.value;
	makeRequest('index.php',1,"record",hours_result,args);
}

function hours_result(result) {
	dialog = document.getElementById("dialog");
	dialog.innerHTML = result;
	show_dialog();
	makeRequest('index.php',1,"draw_mini_hours_report",mini_hours_cp,"");
}

function mini_hours_cp(result) {
	document.getElementById('mini_hours_replace').innerHTML = result;
}

function hours_report() {
	args = new Array();
	args[0] =  document.forms.report_criteria.group_id.value;
	args[1] =  document.forms.report_criteria.user_id.value;
	args[2] =  document.forms.report_criteria.startdate.value;
	args[3] =  document.forms.report_criteria.enddate.value;
	makeRequest('index.php',1,"hours_report",hours_report_cp,args);
}

function hours_report_cp(result) {
	dialog = document.getElementById("hours_report_replace");
	dialog.innerHTML = result;
}

/*
function fieldticket_update_date_select()
{
	if(document.search_form.archive.checked)
	{
		document.search_form.date.options.length=0 ;
		document.search_form.date.options[0]=new Option("Pick a Time Period",null,false,false) ;
		document.search_form.date.options[1]=new Option("1 month","1_month",false,false) ;
		document.search_form.date.options[2]=new Option("2 months","2_month",false,false) ;
		document.search_form.date.options[3]=new Option("3 months","3_month",false,false) ;
		document.search_form.date.options[4]=new Option("6 Months","6_month",false,false) ;
		document.search_form.date.options[5]=new Option("1 Year","12_month",false,false) ;
		update_criteria('archive-on') ;
	}
	else
	{
		document.search_form.date.options.length=0 ;
		document.search_form.date.options[0]=new Option("Pick a Time Period",null,false,false) ;
		document.search_form.date.options[1]=new Option("1 Day","1_day",false,false) ;
		document.search_form.date.options[2]=new Option("2 Days","2_day",false,false) ;
		document.search_form.date.options[3]=new Option("3 Days","3_day",false,false) ;
		document.search_form.date.options[4]=new Option("1 Week","1_week",false,false) ;
		document.search_form.date.options[5]=new Option("2 Weeks","2_week",false,false) ;
		document.search_form.date.options[6]=new Option("3 Weeks","3_week",false,false) ;
		document.search_form.date.options[7]=new Option("1 Month","1_month",false,false) ;
		update_criteria('archive-off') ;
	}
}
*/

function update_criteria(type)
{
	switch(type)
	{
		case "archive":
			var div_ = document.getElementById('archive_criteria');
			var val_ = "Archive:"+document.search_form.archive.checked;
			break;
		case "start_date_month":
			var div_ = document.getElementById('start_date_month_criteria');
			var val_ = "Start Date Month:"+document.search_form.start_date_month.options[document.search_form.start_date_month.selectedIndex].value;
			break;
		case "start_date_day":
			var div_ = document.getElementById('start_date_day_criteria');
			var val_ = "Start Date Day:"+document.search_form.start_date_day.options[document.search_form.start_date_day.selectedIndex].value;
			break;
		case "start_date_year":
			var div_ = document.getElementById('start_date_year_criteria');
			var val_ = "Start Date Year:"+document.search_form.start_date_year.options[document.search_form.start_date_year.selectedIndex].value;
			break;
		case "end_date_month":
			var div_ = document.getElementById('end_date_month_criteria');
			var val_ = "End Date Month:"+document.search_form.end_date_month.options[document.search_form.end_date_month.selectedIndex].value;
			break;
		case "end_date_day":
			var div_ = document.getElementById('end_date_day_criteria');
			var val_ = "End Date Day:"+document.search_form.end_date_day.options[document.search_form.end_date_day.selectedIndex].value;
			break;
		case "end_date_year":
			var div_ = document.getElementById('end_date_year_criteria');
			var val_ = "End Date Year:"+document.search_form.end_date_year.options[document.search_form.end_date_year.selectedIndex].value;
			break;
		case "owner":
			var div_ = document.getElementById('owner_criteria');
			var val_ = "Owner: "+document.search_form.owner.value;
			break;
		case "requestor":
			var div_ = document.getElementById('requestor_criteria');
			var val_ = "Requestor: "+document.search_form.requestor.value;
			break;
		case "ticket_id":
			var div_ = document.getElementById('ticket_id_criteria');
			var val_ = "Ticket ID: "+document.search_form.ticket_id.value;
			break;
	}
	div_.innerHTML = val_ ;
}

function missed_shifts_report() {
	args = new Array();
	args[0] =  document.forms.report_criteria.group_id.value;
	args[1] =  document.forms.report_criteria.user_id.value;
	args[2] =  document.forms.report_criteria.startdate.value;
	args[3] =  document.forms.report_criteria.enddate.value;
	makeRequest('missed_shifts.php',1,"missed_shifts_report",missed_shifts_report_cp,args);
}

function missed_shifts_report_cp(result) {
	dialog = document.getElementById("missed_shifts_replace");
	dialog.innerHTML = result;
}