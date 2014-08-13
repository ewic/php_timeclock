<?php
function submit_message($message) {
	if ($message=='')
		$_SESSION['notifications'][] = "Must enter a message";
	else {
		$message = $_SESSION['dbconn']->real_escape_string($message);
		$author = $_SESSION['user_id'];
		$sql = "INSERT INTO messages (message,user_id,tl_id) VALUES ('".$message."', ".$author.", ".get_team_leader($author).")";
		$result = $_SESSION['dbconn']->query($sql) or die("Error adding message: ".$_SESSION['dbconn']->error);
		$_SESSION['notifications'][]="Added message.";
	}
	
}

function delete_message($message_id) {
	$sql = "DELETE FROM messages WHERE message_id=".$message_id;
	$result = $_SESSION['dbconn']->query($sql) or die("Error removing message: ".$_SESSION['dbconn']->error);
	$_SESSION['notifications'][]="Removed message.";
}

function get_messages($tl_id=NULL) {
	//if the user is an admin, return only messages from other admins
	if (check_app_admin($_SESSION['user_id'])) {
		$sql = "SELECT message,fname,lname,users.user_id FROM messages NATURAL JOIN users WHERE user_id in (SELECT user_id FROM app_admin)";
		$result = $_SESSION['dbconn']->query($sql) or die("Error retrieving messages: ".$_SESSION['dbconn']->error);
		return $result;
	}
		
	else {
		if (!isset($tl_id)) 
			$tl_id = get_team_leader($_SESSION['user_id']);

		$sql = "SELECT message,fname,lname,users.user_id FROM messages NATURAL JOIN users WHERE tl_id=".$tl_id;
		$result = $_SESSION['dbconn']->query($sql) or die("Error retrieving messages: ".$_SESSION['dbconn']->error);
		return $result;
	}
}

function clear_messages($tl_id=NULL) {

}
?>