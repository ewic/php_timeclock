<?php

//Manually adds hours if you are a supervisor
function add_hours($user_id,$tl_id,$amount,$payperiod=NULL) {

	if (!check_supervisor())
		$_SESSION['notifications'][] = "You are not a TL";

	if ($user_id == $tl_id)
		$_SESSION['notifications'][] = "You cannot input hours for yourself";

	else {
		if ($payperiod == NULL)
			$payperiod = current_payperiod();

		$sql = "INSERT INTO additional_hours (user_id,tl_id,amount) VALUES (".$user_id.",".$tl_id.",".$amount.")";
		$_SESSION['dbconn']->query($sql) or die("Error adding hours: ".$_SESSION['dbconn']->error);
		$_SESSION['notifications'][] = "Additional Hours added!";
	}
}


function get_additional_hours($user_id,$payperiod=NULL) {
	if (!$payperiod)
		$payperiod = current_payperiod();

	$ppstart = get_payperiod_start($payperiod);
	$ppend = get_payperiod_end($payperiod);

	$sql =  "SELECT timestamp, user_id, amount FROM additional_hours WHERE user_id=".$user_id;
	$sql .= " AND timestamp > FROM_UNIXTIME(".$ppstart.")";
	$sql .=  " AND timestamp < FROM_UNIXTIME(".$ppend.")";

	$result = $_SESSION['dbconn']->query($sql) or die("Error getting additional hours: ".$_SESSION['dbconn']->error);
	
	$total = 0;
	while ($row = $result->fetch_array())
		$total += $row['amount'];
	return $total;
}


//Make sure it is always fed as a UNIX date
function date_to_payperiod($date) {
	$i = 1;
	while($date >= $i*get_payperiod_length()+get_first_payperiod())
		$i++;
	return $i;
}

function get_payperiod_start($payperiod=NULL) {
	if (!$payperiod)
		$payperiod=current_payperiod();

	$payperiod -= 1;
	$out = get_first_payperiod() + get_payperiod_length()*$payperiod;

	return $out;
}

function get_payperiod_end($payperiod=NULL) {
	if (!$payperiod)
		$payperiod=current_payperiod();

	$out = get_payperiod_start($payperiod)+get_payperiod_length();
	$out -= 1;  //in case anybody punched in at exactly midnight Saturday night at the turn of a payperiod
	return $out;
}

// Returns the current payperiod
function current_payperiod() {

	$date = date('U');

	return date_to_payperiod($date);
}

//TODO
function total_hours($user_id,$payperiod=NULL) {
	if (!$payperiod)
		$payperiod = current_payperiod();
}

//Pass starttime and endtime as UNIX time
function count_hours($punch1,$punch2){
	//window is 5 minutes, or 300 seconds
	$window = 300;
	//interval is 15 minutes, or 900 seconds
	$interval = 900;

	$time = $punch2 - $punch1;
	$i=0;
	while (($i*$interval)-$window < $time) {
		$i++;
	}
	$i -= 1;
	return $i/4;
}

//converts a date to a semester value.
// 0 for fall,
// 1 for spring,
// 2 for summer.
function date_to_semester($date=NULL){
	if ($date == NULL)
		$date = date('U');

	$semester = (intval(date("y",$date))-14)*3;

	if (date('n',$date)<=4)
		$semester += 0;
	else if (date('n',$date)>=9)
		$semester += 2;
	else
		$semester += 1;

	return $semester;
}

//Return the sum of hours for a payperiod
function sum_hours($user_id=NULL,$payperiod=NULL) {
	if (!isset($user_id))
		$user_id = $_SESSION['user_id'];

	if (!isset($payperiod))
		$payperiod = get_current_payperiod();

	$punches = get_punch_data($user_id);
    $inout = '';
    $total = 0;
    while ($row = $punches->fetch_array()) {
      $day = date('n/j',strtotime($row['timestamp']));
      $time = date('h:i a',strtotime($row['timestamp']));
      if ($inout=='in'){
        $inout = 'out';
        $hours = count_hours($intime,mktime(strtotime($row['timestamp'])));
        $total += $hours;
      }
      else {
        $inout = 'in';
        $intime = mktime(strtotime($row['timestamp']));
        $hours = '';
      }
    }

    return $total;
}

//Returns the total number of payperiods.
function get_number_of_payperiods() {
	//How many seconds in a year
	$yearlength = 31536000;
	//if we're in a leap year, add a day to that length
	if (date('L'))
		$yearlength += 86400;
	//Return the length of the year divided by the length of a payperiod.
	$out = intval($yearlength/get_payperiod_length());
	return $out;
}

?>