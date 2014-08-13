function punch(clock_status) {
	args = new Array();
	if (clock_status == 0) {
		args[0] = document.forms.punchclock.work_id.value; 
	}
	else {
		args[0] = clock_status;
	}
	makeRequest('index.php',1,"punch",punch_result,args);
}
function punch_result(result) {
	document.getElementById('dialog').innerHTML = result;
	show_dialog();
	makeRequest('index.php',0,"view_punch_display",mini_punch_display_cp,"");	
	makeRequest('index.php',0,"mini_punch_draw",mini_punch_draw_cp,"");	
	makeRequest('index.php',0,"draw_mini_hours_report",mini_hours_cp,"");
}

function mini_punch_draw_cp(result) {
	document.getElementById('mini_punch_replace').innerHTML = result;
}

function mini_punch_display_cp(result) {
	document.getElementById('punch_display_replace').innerHTML = result;
}
