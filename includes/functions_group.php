<?php

/* Check if the user REALLY wants to delete a group.
call from: new window
ACTION: delete_group
action_type: POST
expects: POST['group_id']
neither: delete_confirm or cancel pushed
*/


function check_delete_group()
{
	echo "<div id='action_box'><div class='box_title'>Delete group confirm</div>";
	$_ESCAPED_GROUP_ID = mysql_real_escape_string($_POST['group_id']);
	include 'includes/db_info.php';

	$query = "select group_name from ".$dbname.".groups where
		group_id = ".$_ESCAPED_GROUP_ID;
	$result = mysql_query($query);
	if ($row = mysql_fetch_array($result)) {
		$group_name = $row['group_name'];
	}
	
	echo "<form method=post action=action.php>";
	echo "You are about to delete the group:<BR><B>";
	echo $group_name."</B>";
	echo "<BR><BR>Click delete to confirm<BR><BR>";
	echo "<input type=submit value=cancel name=cancel>";
	echo "<input type=submit value=delete name=delete_confirm>";
	echo "<input type=hidden name=action value='delete_group'>";
	echo "<input type=hidden name=group_id value=".$_POST['group_id'].">";
	echo "</form>";
	echo "</div>";
}

function add_group() 
{
	echo "<div id='action_box'><div class='box_title'>Group Added</div>";
	$_ESCAPED_GROUP_NAME = $_POST['group_name'];
	if ( $_ESCAPED_GROUP_NAME != "") 
	{
		include 'includes/db_info.php';
		$querycheck = "select group_name from ".$dbname.".groups where (group_name='".$_ESCAPED_GROUP_NAME."')";
		$result = mysql_query($querycheck,$_SESSION['dbconn']);
		if (mysql_num_rows($result) > 0) { echo $_ESCAPED_GROUP_NAME." already exists in the database."; }
		else 
		{

		// ADD THE GROUP
			$query = "insert into ".$dbname.".groups (group_name) values ";
			$query .= "('".$_ESCAPED_GROUP_NAME."')";
			$result = mysql_query($query, $_SESSION['dbconn'])
				or die("Query failed: ".mysql_error());
	
		// MAKE ALL APP ADMINS ALSO GROUP ADMINS (SHOULDN'T BE A HIGH #)
			
			$query = "select group_id from ".$dbname.".groups where group_name = '".$_ESCAPED_GROUP_NAME."'";
			$result = mysql_query($query);
			if ($row = mysql_fetch_array($result)) { $group_id = $row['group_id']; }
			$query = "select * from ".$dbname.".app_admin";
			$result = mysql_query($query);
			while ($row = mysql_fetch_array($result)) 
			{
				$query = "insert into ".$dbname.".group_relate (group_id,user_id,group_admin) values (".$group_id.",".$row['user_id'].",1)";
				$insert_result = mysql_query($query);
			}

			echo $_ESCAPED_GROUP_NAME." Added to database.";
		refresh_parent();
		}
	}		
	else  { echo "You must enter a name for the group you want to add."; }
	echo "</div>";
}

function delete_group() 
{
	echo "<div id='action_box'><div class='box_title'>Group Deleted</div>";
	$_ESCAPED_GROUP_ID = mysql_real_escape_string($_POST['group_id']);
	include 'includes/db_info.php';

	$query = "select group_name from ".$dbname.".groups where group_id = ".$_ESCAPED_GROUP_ID;
	$result = mysql_query($query);

	while ($row = mysql_fetch_array($result))
	{
		$query = "update ".$dbname.".groups set inactive=1,group_name = '".$row['group_name']." prior to ".date("Y-m-d")."' 
			where group_id = ".$_ESCAPED_GROUP_ID;
		$result2 = mysql_query($query,$_SESSION['dbconn']) || die ("Mysql Error: ".mysql_error());
		echo "Group ".$row['group_name']." with id=".$_ESCAPED_GROUP_ID." has been made inactive and has been 
			renamed: ".$row['group_name']." prior to ".date("Y-m-d");

	}
	refresh_parent();
	echo "</div>";
}

?>
