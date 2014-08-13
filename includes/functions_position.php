<?php



function select_position($admin,$selected,$type) {
	include 'includes/db_info.php';

	if ($type == 'onchange_null') { echo "<SELECT name=position_id>"; }
	elseif ($type == 'onchange_normal') 
	{ 
		echo "<SELECT name=position_id onChange=\"submit()\">"; 
	}
	elseif ($type == 'onchange_new') 
	{
		echo "<SELECT name=position_id ";
 		?>Onchange="window.open('','actionwindow','width=410,height=330,top=100,left=100,scrollbars=false,resizeable=false,toolbar=false,menubar=false');this.form.target='actionwindow';this.form.action='action.php';submit();"<?
        	echo ">";
	}
	else { return; }

	echo "<OPTION disabled selected>Select position</OPTION>";
	$result = get_admin_groups($_SESSION['user_id']);
	while ($row = mysql_fetch_array($result))
		{ $admin_groups[$row['group_id']] = 1; }
	$result = get_positions($_SESSION['user_id']);
	while ($row = mysql_fetch_array($result))
		{ $positions[$row['position_id']] = 1; }

	$query = "select * from ".$dbname.".positions,
		 ".$dbname.".groups,".$dbname.".group_relate where 
		positions.group_id = groups.group_id AND
		group_relate.user_id = ".$_SESSION['user_id']."
		AND group_relate.group_id = positions.group_id 
		AND groups.inactive = 0 
		order by group_name,position_name";
	$result = mysql_query($query);

	while ($row = mysql_fetch_array($result)) {
		if ($admin_groups[$row['group_id']] || (!$admin && $positions[$row['position_id']]))
		{
                if ($last_group != $row['group_name'])
                {
                        echo "<option disabled ";
                        echo " style='font-weight: bold; color: #000000;'>";
                        echo $row['group_name']."</option>";
                }

		echo "<OPTION value=".$row['position_id'];
		if ($row['position_id'] == $_SESSION['position_id'] && $selected) {
			echo " selected ";
		}
		echo "> &nbsp ".$row['position_name']."</OPTION>";
	        $last_group = $row['group_name'];
	}
	}
	echo "</SELECT>";
}


// Displays the currently selected position in the format:
// Groupname - positionname

function show_position($position_id) {	
	$o = "";
	$query = "select position_name,group_name from positions,groups where (
		position_id = ".$position_id." AND
		groups.group_id = positions.group_id)";
	$result = mysql_query($query);
	if ($row = mysql_fetch_array($result)) {
		$o .= $row['group_name']." - ".$row['position_name'];
	}	
	return $o;
}

function get_position_name($id) {
	$o = "";
	$query = "select position_name,group_name from positions,groups where (
		position_id = $id AND
		groups.group_id = positions.group_id)";
	$result = mysql_query($query) or die("Mysql error: ".mysql_error());
	if ($row = mysql_fetch_array($result)) {
		$return = array("group" => $row['group_name'],"position" => $row['position_name']);
	}	
	return $return;
}

// use this function in New windows.  NOT the main window.

function change_position() {
	$_ESCAPED_position_id = mysql_real_escape_string($_POST['position_id']);
	$_SESSION['position_id'] = $_ESCAPED_position_id;
	refresh_parent();
	close_window();
}

function add_position() 
{
	echo "<div id='action_box'><div class='box_title'>Position Added</div>";
	$_ESCAPED_POSITION_NAME = $_POST['position_name'];
	$_ESCAPED_GROUP_ID = $_POST['group_id'];
	$_ESCAPED_SDAY = $_POST['sday'];
	$_ESCAPED_EDAY = $_POST['eday'];
	$_ESCAPED_STIME = $_POST['stime'];
	$_ESCAPED_ETIME = $_POST['etime'];
	if ( $_ESCAPED_POSITION_NAME != "" && $_ESCAPED_GROUP_ID != 0
		&& $_ESCAPED_STIME != "" && $_ESCAPED_ETIME != "" 
		&& $_ESCAPED_SDAY != "" && $_ESCAPED_EDAY != "") 
	{
		include 'includes/db_info.php';
		$querycheck = "select position_name from ".$dbname.".positions,".$dbname.".groups where 
			(position_name='".$_ESCAPED_POSITION_NAME."' AND
			positions.group_id = ".$_ESCAPED_GROUP_ID.")";
		$result = mysql_query($querycheck,$_SESSION['dbconn']);
		if (mysql_num_rows($result) > 0) { 
			echo $_ESCAPED_POSITION_NAME." already exists in this group in the database. <BR> If you need to change the times/days of this position, use the edit function."; }
		else 
		{
			$query = "insert into ".$dbname.".positions (position_name,group_id,sday,eday,starttime,endtime) values ";
			$query .= "('".$_ESCAPED_POSITION_NAME."','".$_ESCAPED_GROUP_ID."',".$_ESCAPED_SDAY.",".$_ESCAPED_EDAY.",".$_ESCAPED_STIME.",".$_ESCAPED_ETIME.")";
			$result = mysql_query($query, $_SESSION['dbconn'])
				or die("Query failed: ".mysql_error());
			echo "Position: ".$_ESCAPED_POSITION_NAME." added to database."; 
		}
	
		$query = "select position_id from ".$dbname.".positions where 
			position_name = '".$_ESCAPED_POSITION_NAME."' AND
			group_id = ".$_ESCAPED_GROUP_ID;
		$result = mysql_query($query);
		if ($row = mysql_fetch_array($result))
			{ $position_id = $row['position_id']; }

		$action = "positionmod.php?action=edit_position&position_id=".$position_id;
		parent_goto($action);
	}		
	else  { echo "You must enter a name for the position you want to add and select a group for it to belong to.<BR> You must also select a starting day/time and an ending day/time "; }
	echo "</div>";
}

function delete_position() 
{
	echo "<div id='action_box'><div class='box_title'>Position Deleted</div>";
	$_ESCAPED_POSITION_ID = mysql_real_escape_string($_POST['position_id']);
	if ($_ESCAPED_POSITION_ID == $_SESSION['position_id']) { unset($_SESSION['position_id']); }
	include 'includes/db_info.php';

	$query = "select position_name,group_name from ".$dbname.".positions,".$dbname.".groups where
		positions.position_id = ".$_ESCAPED_POSITION_ID." AND 
		positions.group_id = groups.group_id";
	$result = mysql_query($query);
	
	if ($row = mysql_fetch_array($result)) {
		$group_name = $row['group_name'];
		$position_name = $row['position_name'];
	}

	$query = "delete from ".$dbname.".positions where position_id = ".$_ESCAPED_POSITION_ID;
	$result = mysql_query($query,$_SESSION['dbconn']) || die ("mysql error: ".mysql_error());
	$query = "delete from ".$dbname.".position_relate where position_id = ".$_ESCAPED_POSITION_ID;
	$result = mysql_query($query,$_SESSION['dbconn']) || die ("mysql error: ".mysql_error());
	$query = "delete from ".$dbname.".sched where position_id = ".$_ESCAPED_POSITION_ID;
	$result = mysql_query($query,$_SESSION['dbconn']) || die ("mysql error: ".mysql_error());

	echo "Position: ".$position_name." has been deleted from the ".$group_name." group.  All of the user 
		relationships to this position have also been deleted as well as the schedule associated with 
		this position.";	
	echo "</div>";
	$action = "positionmod.php";
	parent_goto($action);
}

function change_time() {
	echo "<div id='action_box'><div class='box_title'>Position time and day change</div>";

	$_ESCAPED_POSITION_ID = mysql_real_escape_string($_POST['position_id']);
	$_ESCAPED_SDAY = mysql_real_escape_string($_POST['sday']);
	$_ESCAPED_EDAY = mysql_real_escape_string($_POST['eday']);
	$_ESCAPED_STIME = mysql_real_escape_string($_POST['stime']);
	$_ESCAPED_ETIME = mysql_real_escape_string($_POST['etime']);

	if ($_ESCAPED_SDAY > $_ESCAPED_EDAY || $_ESCAPED_STIME >= $_ESCAPED_ETIME) {
		echo "The start day must be the same or earlier than the end day.";
		echo "<BR>&nbsp&nbsp AND<BR>";
		echo "The start time must be earlier than the end time";  
	}
	else
	{
		include 'includes/db_info.php';
		$query = "update ".$dbname.".positions set sday=".$_ESCAPED_SDAY.",eday=".$_ESCAPED_EDAY.",starttime=".$_ESCAPED_STIME.",endtime=".$_ESCAPED_ETIME." where position_id = ".$_ESCAPED_POSITION_ID;
		mysql_query($query);

/* Delete days from the regular schedule that are before 
new start day or after the new end day. */

		$query = "delete from ".$dbname.".sched where position_id = ".$_ESCAPED_POSITION_ID." AND ( 
			day < ".$_ESCAPED_SDAY." OR day > ".$_ESCAPED_EDAY.")";
		mysql_query($query);

/* Find shifts on the dynamic schedule for the position we are changing
if that that shift's date is on a day less than the new start 
day or greater than the new end day, delete it. */

		$query = "select shift_id,date from ".$dbname.".dyn_sched where position_id = ".$_ESCAPED_POSITION_ID;
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result))
		{
			$day_of_week = date("w",strtotime($row['date']));

			if (($day_of_week < $_ESCAPED_SDAY) || ($day_of_week > $_ESCAPED_EDAY))
			{
				$query = "delete from ".$dbname.".dyn_sched where shift_id = ".$row['shift_id'];
				mysql_query($query);
			}
		}

/* Delete all shifts on the regular schedule that end before the new start time
or start before the new end time. */

		$query = "delete from ".$dbname.".sched where (stime >= ".$_ESCAPED_ETIME." OR etime <= ".$_ESCAPED_STIME.") AND position_id = ".$_ESCAPED_POSITION_ID;
		mysql_query($query);

/* Delete all shifts on the dynamic schedule that end before the new start time
or start before the new end time. */

		$query = "delete from ".$dbname.".dyn_sched where (stime >= ".$_ESCAPED_ETIME." OR etime <= ".$_ESCAPED_STIME.") AND position_id = ".$_ESCAPED_POSITION_ID;
		mysql_query($query);

/* Set start of the shift to the new start time if the shift straddles the new
start time  - Regular schedule */

		$query = "update ".$dbname.".sched set stime = ".$_ESCAPED_STIME." where position_id = ".$_ESCAPED_POSITION_ID." AND 
			stime < ".$_ESCAPED_STIME;
		mysql_query($query);

/* Same for the end time*/

		$query = "update ".$dbname.".sched set etime = ".$_ESCAPED_ETIME." where position_id = ".$_ESCAPED_POSITION_ID." AND 
			etime > ".$_ESCAPED_ETIME;
		mysql_query($query);

/* Set start of the shift to the new start time if the shift straddles the new
start time - Dynamic Schedule */

		$query = "update ".$dbname.".dyn_sched set stime = ".$_ESCAPED_STIME." where position_id = ".$_ESCAPED_POSITION_ID." AND 
			stime < ".$_ESCAPED_STIME;
		mysql_query($query);

/* Same for the end time */

		$query = "update ".$dbname.".dyn_sched set etime = ".$_ESCAPED_ETIME." where position_id = ".$_ESCAPED_POSITION_ID." AND 
			etime > ".$_ESCAPED_ETIME;
		mysql_query($query);

/* refresh the parent page */
		$url = "./positionmod.php?action=edit_position&position_id=";
		$url .= $_ESCAPED_POSITION_ID;
		parent_goto($url);
		close_window();
	}	
	echo "</div>";
	
}

function get_position_info($position_id) {
	if (isset($position_id) && $position_id != "") {
		include 'includes/db_info.php';
    		$query = "select sday,eday,starttime,endtime from ".$dbname.".positions
			where position_id = '".$position_id."'";
		$result = mysql_query($query);
		if ($row = mysql_fetch_array($result))
		{
			return $row;
		}
	}
	return false;
}

?>
