function draw_week(week,position) {
	args = new Array();
	args[0] = '0';
	args[1]=  week;
	args[2] = position;
	makeRequest('index.php',1,"draw_w_schedule",draw_week_cp,args);
}

function draw_week_cp(result) {
	target = document.getElementById("schedule_replace");
	target.innerHTML = result;
}

function draw_day(time) {
	args = new Array();
	args[0] = '0';
	args[1]=  time;
	makeRequest('index.php',1,"draw_d_schedule",draw_day_cp,args);
}

function draw_day_cp(result) {
	target = document.getElementById("schedule_replace");
	target.innerHTML = result;
}

function selectday() {
	time = document.forms.select_day.day.value;
	draw_day(time);
}

function selectposition(regular,week) {
	args = new Array();
	args[0] = regular;
	args[1] =  week;
	args[2] = document.forms.select_position.position_id.value;
	clear_shift_info();
	makeRequest('index.php',1,"draw_w_schedule",draw_week_cp,args);
}


function shift_info(regular,id) {
	makeRequest('index.php',2,"shift_info",shift_info_cp,shift_info.arguments);
}

function shift_info_cp(result) {
	target = document.getElementById("shift_info");
	target.innerHTML = result;
}


function mini_sched(week) {
	makeRequest('index.php',0,"mini_sched_draw",mini_sched_cp,mini_sched.arguments);
}

function mini_sched_cp(result) {
	document.getElementById('mini_sched_replace').innerHTML = result;
}

function mini_take(week) {
	makeRequest('index.php',0,"mini_take_draw",mini_take_cp,mini_take.arguments);
}

function mini_take_cp(result) {
	document.getElementById('mini_take_replace').innerHTML = result;
}


function giveup(id,week) {
	makeRequest('index.php',1,"giveup",trade_cp,giveup.arguments);
}


function take_select(id,week) {
	makeRequest('index.php',1,"take_select",trade_cp,take_select.arguments);
}

function take(id,week) {
	args = new Array();
	args[0] = id;
	args[1] = document.forms.selecttime.stime.value; 
	args[2] = document.forms.selecttime.etime.value; 
	makeRequest('index.php',1,"take",trade_cp,args);
}

function trade_cp(result) {
     	dialog = document.getElementById("dialog");
	dialog.innerHTML = result;
	show_dialog();
	args = new Array();
	args[0] = '0';
	makeRequest('index.php',0,"mini_take_draw",mini_take_cp,args[0]);
	makeRequest('index.php',0,"mini_sched_draw",mini_sched_cp,args[0]);
}

function delete_shift_check(regular,id) {
	makeRequest('index.php',1,"delete_shift_check",display_result,delete_shift_check.arguments);
}

function delete_shift(regular,id) {
	shift = document.getElementById(id);
	shift.innerHTML = "&nbsp;";
	shift.className="blank";
	shift.onclick = "";
	document.getElementById("shift_info").innerHTML = "Shift Deleted";
	makeRequest('index.php',1,"delete_shift",display_result,delete_shift.arguments);
}	

function report_missed_shift(shift_id) {
	makeRequest('index.php',0,"report_missed_shift",report_missed_shift_cp,report_missed_shift.arguments);
}

function report_missed_shift_cp(result) {
     	dialog = document.getElementById("dialog");
	dialog.innerHTML = result;
	show_dialog();
}