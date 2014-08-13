<?php

function add_goal($goal,$due_date) {
	$sql = "INSERT INTO goals (user_id,goal,due_date,completed) VALUES (";
	$sql .= $_SESSION['user_id'].', ';
	$sql .= '"'.$goal.'", ';
	$sql .= '"'.$due_date.'", ';
	$sql .= '0)';
	$result = $_SESSION['dbconn']->query($sql) or die("Error submitting goal: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][]="Added new Goal, '".$goal.".";
}

function get_goals() {

	$sql = "SELECT * FROM goals WHERE user_id=".$_SESSION['user_id'];
	
	$result = $_SESSION['dbconn']->query($sql) or die("Error getting goals: ".$_SESSION['dbconn']->error);
	return $result;
}

function get_goal_points($user_id) {
	$sql = "SELECT goal_id,level FROM goals WHERE user_id=".$user_id;
	$result = $_SESSION['dbconn']->query($sql);

	$out = $result->fetch_array();

	
}

?>