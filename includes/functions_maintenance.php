<?php

function update_global($field, $value) {
	global $console;
	$console[] = 'updating global: '.$field.' with value: '.$value;

	$sql = "UPDATE systemglobals SET value=".$value." WHERE field='".$field."'";

	$_SESSION['dbconn']->query($sql) or die("Error updating global ".$field.": ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Updated global: ".$field;
}

//Make sure to pass date as yyyy-mm-dd
function update_first_payperiod($date) {

	$date = explode("-",$date);
	$date = mktime(0,0,0,$date[1],$date[2],$date[0]);

	update_global("firstpp",$date);
}

//Make sure to pass $length number of days
function update_payperiod_length($length) {
	$length = $length*86400;

	update_global("pplength", $length);
}

function get_global($field) {
	$sql = "SELECT value FROM systemglobals WHERE field='".$field."'";
	$result = $_SESSION['dbconn']->query($sql) or die("Error getting field ".$field.": ".$_SESSION['dbconn']->error);

	$out = $result->fetch_row();
	return $out[0];
}

function get_first_payperiod() {
	return get_global('firstpp');
}

function get_payperiod_length() {
	return get_global('pplength');
}

?>