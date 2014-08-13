<?php

//Pass it an array of data
function add_qual($qualinfo) {
	$sql = "INSERT INTO quals (name,url,level,value) VALUES (";
	$sql .= "'".$qualinfo['name']."', ";
	$sql .= "'".$qualinfo['url']."', ";
	$sql .= $qualinfo['level'].", ";
	$sql .= $qualinfo['value'].")";

	$_SESSION['dbconn']->query($sql) or die("Error adding qual: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "New Qual submitted!";
}

//Updates a qual with info from an array.
function edit_qual($qualinfo) {
	$sql = "UPDATE quals ";
	$sql .= "SET name='".$qualinfo['name']."', ";
	$sql .= "url='".$qualinfo['url']."', ";
	$sql .= "level=".$qualinfo['level'].", ";
	$sql .= "value=".$qualinfo['value']." ";
	$sql .= "WHERE qual_id=".$qualinfo['qual_id'];

	//echo $sql;
	$_SESSION['dbconn']->query($sql) or die("Error editing qual: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Qual updated";
}

//Marks a qual as finished for a user
function finish_qual($qual_id,$user_id,$tl_id=null,$date=null) {
	if (!isset($tl_id))
		$tl_id = $_SESSION['user_id'];

	if (!isset($date))
		$date = date('Y-m-d');

	$sql = "INSERT INTO qual_progress VALUES (";
	$sql .= $qual_id.', ';
	$sql .= $user_id.', ';
	$sql .= $tl_id.', ';
	$sql .= '"'.$date.'")';
	
	//echo $sql;
	$_SESSION['dbconn']->query($sql) or die("Error finishing qual: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Qual completed";
}

function unfinish_qual($qual_id,$user_id) {
	$sql = "DELETE FROM qual_progress WHERE user_id=".$user_id." AND qual_id=".$qual_id;
	
	//echo $sql;
	$_SESSION['dbconn']->query($sql) or die("Error unfinishing qual: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = "Removed qual completion";
}

function get_qual_info($qual_id) {
	$sql = "SELECT * FROM quals WHERE qual_id=".$qual_id;
	$result = $_SESSION['dbconn']->query($sql) or die("Error retrieving qual info: ".$_SESSION['dbconn']->error);
	return $result->fetch_assoc();
}

//Activate/Deactivate a qual
function deactivate_qual($qual_id) {
	$sql = "UPDATE quals SET inactive=1 WHERE qual_id=".$qual_id;
	$_SESSION['dbconn']->query($sql) or die("Error deactivating qual: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = 'Qual Deactivated';
}

function reactivate_qual($qual_id) {
	$sql = "UPDATE quals SET inactive=0 WHERE qual_id=".$qual_id;
	$_SESSION['dbconn']->query($sql) or die("Error activating qual: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][] = 'Qual Reactivated';
}

function toggle_qual($qual_id) {
	$sql = "SELECT inactive FROM quals WHERE qual_id=".$qual_id;
	$result = $_SESSION['dbconn']->query($sql);

	$result = $result->fetch_array();
	if ($result[0]=='1')
		reactivate_qual($qual_id);
	else
		deactivate_qual($qual_id);

}

//Retrieve the point value of the qual
function get_qual_point_value($qual_id) {
	$out = get_qual_info($qual_id);
	return $out['value'];
}

//Fetch the list of quals
//$include_inactive will decide if the result includes inactive quals or not
function get_quals($include_inactive=false) {
	$sql = "SELECT * FROM quals";
	if ($include_inactive==false)
		 $sql .= " WHERE inactive=0";
	$sql .= ' ORDER BY inactive,level,name';
	$result = $_SESSION['dbconn']->query($sql) or die("Error fetching quals: ".$_SESSION['dbconn']->error);
	return $result;
}

//Returns the total point value of all active quals
function get_qual_point_total() {
	$sql = "SELECT SUM(value) FROM quals WHERE inactive=0";
	$result = $_SESSION['dbconn']->query($sql);
	$out = $result->fetch_array();
	$out = $out[0];
	return $out;
}

function get_qual_progress($user_id) {
	$sql = "SELECT qual_id, name as qualname, level, value,fname as tl_fname,lname as tl_lname,completion_date FROM qual_progress JOIN users on (qual_progress.tl_id = users.user_id) NATURAL JOIN quals WHERE qual_progress.user_id=".$user_id." ORDER BY level, completion_date,qualname";

	$result = $_SESSION['dbconn']->query($sql);
	return $result;
}

//Returns an array of everybody's qual progress
function get_all_qual_progress() {
	$sql = "SELECT user_id, value FROM qual_progress NATURAL JOIN quals WHERE inactive=0 AND user_id NOT IN (SELECT user_id FROM app_admin UNION SELECT user_id FROM app_supervisor)";
	$result = $_SESSION['dbconn']->query($sql);
	$out = $result->fetch_array();

	return $out;
}

function qual_level_to_levelname($level){
	if ($level == 0)
		return 'page';
	else if ($level == 1)
		return 'squire';
	else if ($level == 2)
		return 'specialist';
	else 
		return $level;

}

function get_progress($user_id=NULL) {
	if (!isset($user_id))
		$user_id = $_SESSION['user_id'];

	$sql = "SELECT sum(value) AS progress FROM qual_progress NATURAL JOIN quals WHERE user_id=".$user_id;
	
	$result = $_SESSION['dbconn']->query($sql) or die("Error fetching qual progress: ".$_SESSION['dbconn']->error);
	$out = $result->fetch_array();
	return $out[0];
}

//Returns a list of all quals not completed by the user
function get_not_done_quals($user_id) {
	$sql = "SELECT qual_id,name, value, level FROM quals WHERE inactive=0 AND qual_id NOT IN (SELECT qual_id FROM qual_progress WHERE user_id=".$user_id.") ORDER BY level,name";
	$result = $_SESSION['dbconn']->query($sql) or die("Error fetching list of quals not done.");

	return $result;
}

//Returns the level of the user.
// For instance, if a user has all page quals done, but not all squires, return 1.
// If a user does not have all page quals done, return 0.
// If a user has all page quals and more than half the squire quals, return 2.
function get_level($user_id=NULL) {
	if (!isset($user_id))
		$user_id = $_SESSION['user_id'];

	//Create the empty arrays with the right amount of indexes.
	$qual_levels = array_fill(0,3,0);
	$user_levels = array_fill(0,3,0);

	$levels = count_quals();

	while ($level = $levels->fetch_array())
		$qual_levels[$level['level']] = $level['number_of_quals'];

	//This statement results in the total amount of quals this user has completed grouped by level.
	// (it's super clever!)
	$sql = "SELECT level,count(level) as number_completed FROM quals NATURAL JOIN qual_progress NATURAL JOIN users WHERE user_id=".$user_id;
	//Deselect all inactive quals.
	$sql .= " AND quals.inactive=0";
	//Group by
	$sql .= " GROUP BY level";

	$result = $_SESSION['dbconn']->query($sql) or die("Error fetching qual progress: ".$_SESSION['dbconn']->error);

	while ($level = $result->fetch_array()) 
		$user_levels[$level['level']] = $level['number_completed'];

	//Compare the two arrays to see what level the user is.
	if ($user_levels[0]<$qual_levels[0])
		return 0;
	//If the user has not done half the squire quals, return 1.
	else if ($user_levels[1]<floor($qual_levels[1]/2))
		return 1;
	//If the user has done at least half, but not all, return 2.
	else if ($user_levels[1]<$qual_levels[1])
		return 2;

	else
		return 3;
}

function user_level_to_levelname($level) {
	if ($level==0)
		return 'Page';
	if ($level==1)
		return 'Squire 1';
	if ($level==2)
		return 'Squire 2';
	if ($level==3)
		return 'Specialist';
}

//Feed it the level of a set of quals, it will return the total
// point value of all quals of that level.
function get_qual_level_value($level) {
	$sql = "SELECT sum(value) FROM quals WHERE inactive=0 AND level=".$level;
	$result = $_SESSION['dbconn']->query($sql) or die($_SESSION['dbconn']->error);

	$out = $result->fetch_array();
	return $out[0];
}

//Returns the total number of quals, grouped by level.
function count_quals($inactive=false) {
	$sql = "SELECT level,count(level) as number_of_quals FROM quals";

	//Default action should exclude inactives.
	if (!$inactive)
		$sql .= " WHERE inactive=0";

	$sql .= " GROUP BY level ORDER BY level";

	$result = $_SESSION['dbconn']->query($sql) or die("Error counting quals: ".$_SESSION['dbconn']->error);

	return $result;
}

?>