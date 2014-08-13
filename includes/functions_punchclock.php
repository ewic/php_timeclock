<?php

//Punch in by barcode
function punch($barcode) {

	$user_id = barcode_to_id($barcode);

	if (!$user_id)
		return false;

	else {
		$sql = "INSERT INTO punchclock (user_id) VALUE (".$user_id.")";
		$_SESSION['dbconn']->query($sql) or die("Error punching in: ".$_SESSION['dbconn']->error);
		$_SESSION['notifications'][] = $user_id." punched.";
		return true;
	}

}

//takes a barcode and returns the corresponding id
function barcode_to_id($barcode) {

	$sql = "SELECT user_id FROM users WHERE barcode = ".$barcode;
	$result = $_SESSION['dbconn']->query($sql) or die($_SESSION['dbconn']->error);

	if ($result->num_rows == 0)
		return false;
	else {
		$row = $result->fetch_row();
		return $row[0];
	}
}

//takes an id and returns the corresponding barcode
function id_to_barcode($user_id) {
	$sql = "SELECT barcode FROM users WHERE user_id = ".$user_id;
	$result = $_SESSION['dbconn']->query($sql) or die($_SESSION['dbconn']->error);

	if ($result->num_rows == 0)
		return false;
	else {
		$row = $result->fetch_row();
		return $row[0];
	}
}

//Returns a mysql object containing all the punches in a payperiod for a user.
function get_punch_data($user_id,$payperiod=NULL) {
	if (!$payperiod)
		$payperiod = current_payperiod();

	$ppstart = get_payperiod_start($payperiod);
	$ppend = get_payperiod_end($payperiod);

	$sql = "SELECT id,timestamp FROM punchclock WHERE ";
	$sql .= "user_id=".$user_id;
	$sql .= " AND timestamp > FROM_UNIXTIME(".$ppstart.")";
	$sql .=  " AND timestamp < FROM_UNIXTIME(".$ppend.")";
	
	$result = $_SESSION['dbconn']->query($sql) or die("Error getting punchdata: ".$_SESSION['dbconn']->error);
	return $result;
}

// Status is determined by number of punches
// If punch amount is odd, then punched in, if even, then punched out
// Return 1 if in, 0 if out.
function is_punched_in($user_id) {
	$sql = "SELECT id FROM punchclock WHERE user_id='".$user_id."' ORDER BY timestamp";
	$result = $_SESSION['dbconn']->query($sql);
	return $result->num_rows%2;
}

//If a user forgets to punch
function insert_punch($user_id,$timestamp) {
	$sql = "INSERT INTO punchclock (user_id, timestamp) VALUES (".$user_id.", ".$timestamp.")";
	$_SESSION['dbconn']->query($sql) or die("Error punching in: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = $user_id." punched.";
	return true;
}
?>