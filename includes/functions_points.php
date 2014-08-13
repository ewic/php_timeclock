<?php

function add_points($note,$value,$user_id,$tl_id=null) {


	if (!isset($tl_id))
		$tl_id = $_SESSION['user_id'];

	$sql = "INSERT INTO points (note,value,user_id,tl_id) VALUES (";
	$sql .= "'".$note."',";
	$sql .= $value.', ';
	$sql .= $user_id.', ';
	$sql .= $tl_id.')';

	//echo $sql;
	$_SESSION['dbconn']->query($sql) or die("Error adding points: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Added points";
}

function remove_points($point_id) {
	$sql = "DELETE FROM points WHERE point_id=".$point_id;

	$_SESSION['dbconn']->query($sql);
	$_SESSION['notifications'][] = "Removed points";
}

//Asks mysql for the sum of the points this user has gotten and returns that value.
function get_points_total($user_id) {
	$sql = "SELECT IFNULL(sum(value),0) FROM points WHERE user_id=".$user_id;

	$result = $_SESSION['dbconn']->query($sql);
	$out = $result->fetch_array();

	return $out[0];
}

//Returns a mysql object containing all the points a user has been given.
function get_points($user_id) {
	$sql = "SELECT point_id, note, value, fname as tl_fname,lname as tl_lname, timestamp FROM points LEFT JOIN users ON points.tl_id=users.user_id WHERE points.user_id=".$user_id;
	$result = $_SESSION['dbconn']->query($sql) or die("Error getting points: ".$_SESSION['dbconn']->error);

	return $result;
}

function get_team_point_total($tl_id) {
	//Check to make sure this guy's a team leader
	$sql = 'SELECT * FROM app_supervisor WHERE user_id='.$tl_id;
	$result = $_SESSION['dbconn']->query($sql);
	if ($result->num_rows==0)
		$_SESSION['notificiations'][] = 'This user is not a team leader';
	//If this user is a team leader, do the thing.
	else {
		$sql = 'SELECT sum(value) FROM points WHERE user_id IN (SELECT user_id FROM teams WHERE tl_id='.$tl_id.')';
		$result = $_SESSION['dbconn']->query($sql) or die("Error finding sum: ".$_SESSION['dbconn']->error);
		$out = $result->fetch_array();
		return $out[0];
	}
}

?>