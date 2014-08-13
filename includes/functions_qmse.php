<?php

/*Adds a quest into the database
*/
function add_appt($apptinfo){

	if (!validate_appt($apptinfo)){
		return null;
	}

	else {
		$sql = "INSERT INTO qmse (submitter_id,tech_id,tt,type,building,room,datetime) VALUES ("; 
		$sql .= "'".$apptinfo['submitter_id']."', "; 
		$sql .= "'".$apptinfo['tech_id']."', "; 
		$sql .= "'".$apptinfo['tt']."', ";
		$sql .= "'".$apptinfo['type']."', ";
		$sql .= "'".$apptinfo['building']."', ";
		$sql .= "'".$apptinfo['room']."', ";
		$sql .= "'".$apptinfo['datetime']."')";

		//echo $sql;

		$_SESSION['dbconn']->query($sql) or die("Error adding quest: ".$_SESSION['dbconn']->error);
		$_SESSION['notifications'][] = "New Quest submitted!";
	}
}

// Converts a type integer to a string, based on the global defined in config.php
function appt_type_to_string($type) {
	global $appt_types;
	
	if ($type < count($appt_types))
		return $appt_types[$type];
	else
		return $type;
}

function appt_type_to_classname($type) {
	global $appt_types;

	if ($type < count($appt_types))
		return str_replace(" ","-",$appt_types[$type]);
	else
		return $type;
}

/*Removes a quest from the database
*/
function appt_delete($quest_id) {
	$sql = "DELETE FROM qmse WHERE quest_id=".$quest_id;

	$_SESSION['dbconn']->query($sql) or die("Error deleting quest: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Quest deleted";
}

/*Modifies a quest that has already been inputted into the system
*/
function appt_edit($apptinfo){

	$sql = "UPDATE qmse ";
	$sql .= "SET quest_id='".$apptinfo['quest_id']."', ";
	$sql .= "tech_id='".$apptinfo['tech_id']."', ";
	$sql .= "tt='".$apptinfo['tt']."', ";
	$sql .= "building='".$apptinfo['building']."', ";
	$sql .= "room='".$apptinfo['room']."', ";
	$sql .= "type='".$apptinfo['type']."'";

	$sql .= " WHERE quest_id=".$apptinfo['quest_id'];

	$_SESSION['dbconn']->query($sql) or die("Error editing quest: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Quest updated";
}

//True if the appt is valid.
function validate_appt($apptinfo) {
	//If the appt is a quest
	if ($apptinfo['type']==0) {
		$sql = "SELECT * FROM qmse WHERE datetime='".$apptinfo['datetime']."'";
		$result = $_SESSION['dbconn']->query($sql);
		if ($result->num_rows!=0) {
			$_SESSION['notifications'][] = 'This quest time is full!';
			return false;
		}
		else 
			return true;
	}
	//If the appt is a diagnostic
	else if ($apptinfo['type']==1) {
		$sql = "SELECT * FROM qmse WHERE datetime='".$apptinfo['datetime']."'";
		$result = $_SESSION['dbconn']->query($sql);
		if ($result->num_rows!=0) {
			$_SESSION['notifications'][] = 'This diagnostic time is full!';
			return false;
		}
		else 
			return true;
	}
	
	//If the appt is a VC
	else if ($apptinfo['type']==2) {
		$sql = "SELECT * FROM qmse WHERE datetime='".$apptinfo['datetime']."'";
		$result = $_SESSION['dbconn']->query($sql);
		if ($result->num_rows>=3) {
			$_SESSION['notifications'][] = 'This night is full!';
			return false;
		}
		else 
			return true;
	}
}


function get_appts($type=NULL) {
	$sql = "SELECT * FROM qmse";
	//If type is null, this will only return appointments from this month. (the default action)
	if (!isset($type))
		$sql .= " WHERE datetime >= '".date("Y-m-01 00:00:00")."'";
	//if type is 'week', this will only return appts from this week.
	else if ($type=="week") 
		$sql .= " WHERE datetime >= '".date("Y-m-d 00:00:00",strtotime('last monday'))."' AND datetime <='".date("Y-m-d 00:00:00",strtotime('monday'))."'";

	$sql .= " ORDER BY datetime";
	//echo $sql;
	$result = $_SESSION['dbconn']->query($sql);
	return $result;
}

function get_appt_info($appt_id) {
	$sql = "SELECT * FROM qmse WHERE quest_id=".$appt_id;

	$result = $_SESSION['dbconn']->query($sql);
	return $result->fetch_array();
}

//Returns true if there is supposed to be an appointment ringing right now
function is_appointment() {
	$now = date('Y-m-d H:i:00');
	$five_minutes_from_now = date('Y-m-d H:i:00',strtotime($now)+300);
	$five_minutes_ago = date('Y-m-d H:i:00',strtotime($now)-300);

	echo $now;
	echo '<br />';
	echo $five_minutes_from_now;
	echo '<br />';
	echo $five_minutes_ago;

	//Fetch any appointment that may be within 5 minutes from now
	$sql = "SELECT quest_id FROM qmse WHERE datetime<'".$five_minutes_from_now."' AND datetime>'".$five_minutes_ago."'";

	$result = $_SESSION['dbconn']->query($sql);

	echo $sql;

	//If there is an appointment within 5 minutes of now, return true;
	if ($result->num_rows!=0){
		echo "Appointment now!";
		return true;
	}

	else
		return false;
}

?>