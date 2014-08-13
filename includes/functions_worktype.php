<?php


/* This function might need to change slightly as there are two incarnations of it that are exactly
the same except for how submit works.
It will search for:
groups and positions that a user belongs to OR
groups and all positions of that group that a user is an admin of.
The $selected variable is there for some cases (selecting position to modify) where the currently selected
position is not a desired thing to have selected by default in this menu.
*/


function select_worktype() {
	$o = "";
	$query = "select work_id,work_name,group_name from 
		worktype,groups,group_relate
		where worktype.group_id = groups.group_id AND
		group_relate.group_id = groups.group_id AND
		worktype.inactive = 0 AND groups.inactive = 0 AND
		group_relate.user_id = ".$_SESSION['user_id']." 
		order by group_name,work_name";
	$result = mysql_query($query);
	$o .= "<option selected disabled=true value='0'>Select work type</option>\n";
	while ($row=mysql_fetch_array($result)) {
		if ($last_group != $row['group_name'])
        	{
       			$o .= "<option disabled=true style='font-weight: bold; color: #000000;'>";
			$o .= "<B>".$row['group_name']."</B></option>";
        	}
       		$o .= "<option value='".$row['work_id']."'>";
      		$o .= " &nbsp ";
       		$o .= $row['work_name']."</option>\n";
        	$last_group = $row['group_name'];
	}
	return $o;
}

function select_worktype_or_group() {

        $query = "select work_id,work_name,group_name,groups.group_id from 
		worktype,groups,group_relate
                where worktype.group_id = groups.group_id AND
                group_relate.group_id = groups.group_id AND
                group_relate.user_id = ".$_SESSION['user_id']." order by group_name,work_name";
        $result = mysql_query($query,$_SESSION['dbconn']);

        while ($row=mysql_fetch_array($result)) {
                if ($last_group != $row['group_name'])
                {
                        $o .= "<option value=";
			$neg_group = 0 - $row['group_id'];
			$o .= $neg_group; 
			$o .= " style='font-weight: bold; color: #000000;' ";
			if (isset($_POST['group_id']) && $_POST['group_id'] == 0-$row['group_id']) { $o .= " selected "; }	
                        $o .= "><B>".$row['group_name']."</B></option>";
                }
                $o .= "<option value='".$row['work_id']."' ";
		if (isset($_POST['group_id']) && $_POST['group_id'] == $row['work_id']) { $o .= " selected "; }
                $o .= "> &nbsp ";
                $o .= $row['work_name']."</option>";
                $last_group = $row['group_name'];
	}
	return $o;
}

// Displays the worktype and group information in the format:
// Groupname - worktype name

function show_worktype($work_id) {
        include 'includes/db_info.php';
        $query = "select work_name,group_name from ".$dbname.".worktype,
                ".$dbname.".groups where (
                work_id = ".$work_id." AND
                groups.group_id = worktype.group_id)";
        $result = mysql_query($query);
        if ($row = mysql_fetch_array($result)) {
                $o .= $row['group_name']." - ".$row['work_name'];
        }
	return $o;
}




/* function to add a particular worktype
call from: new_window
ACTION: add_worktype
action_type: post
expects: POST['worktype_name'], POST['group_id']

basic error checking checks for:
duplicate worktype names in the same group
no worktype name entered
*/

function add_worktype() 
{
	echo "<div id='action_box'><div class='box_title'>Worktype Added</div>";
	$_ESCAPED_WORKTYPE_NAME = $_POST['worktype_name'];
	$_ESCAPED_GROUP_ID = $_POST['group_id'];
	if ( $_ESCAPED_WORKTYPE_NAME != "" && $_ESCAPED_GROUP_ID != 0) 
	{
		include 'includes/db_info.php';
		$querycheck = "select work_name from ".$dbname.".worktype,".$dbname.".groups where 
			(work_name='".$_ESCAPED_WORKTYPE_NAME."' AND
			worktype.group_id = ".$_ESCAPED_GROUP_ID." AND
			worktype.inactive = 0)";
		$result = mysql_query($querycheck,$_SESSION['dbconn']);
		if (mysql_num_rows($result) > 0) { 
			echo $_ESCAPED_WORKTYPE_NAME." already exists in this group in the database."; }
		else 
		{
			$query = "insert into ".$dbname.".worktype (work_name,group_id) values ";
			$query .= "('".$_ESCAPED_WORKTYPE_NAME."','".$_ESCAPED_GROUP_ID."')";
			$result = mysql_query($query, $_SESSION['dbconn'])
				or die("Query failed: ".mysql_error());
			echo "Worktype: ".$_ESCAPED_WORKTYPE_NAME." added to database."; 
		}
	}		
	else  { echo "You must enter a name for the worktype you want to add and select a group for it to belong to."; }
	echo "</div>";
	refresh_parent();
}

/* Function that asks the user if they REALLY want to delete a worktype.
call from: new window
ACTION: delete_worktype 
action_type: POST
expects: POST['worktype_id']
when cancel nor delete_confirm button has been selected yet.
*/

function check_delete_worktype()
{
	echo "<div id='action_box'><div class='box_title'>Delete worktype confirm</div>";
	$_ESCAPED_WORKTYPE_ID = mysql_real_escape_string($_POST['worktype_id']);
	include 'includes/db_info.php';

	$query = "select work_name,group_name from ".$dbname.".worktype,".$dbname.".groups where
		worktype.work_id = ".$_ESCAPED_WORKTYPE_ID." AND 
		worktype.group_id = groups.group_id";
	$result = mysql_query($query);
	if ($row = mysql_fetch_array($result)) {
		$group_name = $row['group_name'];
		$work_name = $row['work_name'];
	}
	
	echo "<form method=post action=action.php>";
	echo "You are about to delete the worktype:<BR><B>";
	echo $group_name." - ".$work_name."</B>";
	echo "<BR><BR>Click delete to confirm<BR><BR>";
	echo "<input type=submit value=cancel name=cancel>";
	echo "<input type=submit value=delete name=delete_confirm>";
	echo "<input type=hidden name=action value='delete_worktype'>";
	echo "<input type=hidden name=worktype_id value=".$_POST['worktype_id'].">";
	echo "</form>";
	echo "</div>";
}

/* Similar to above delete worktype function 
call from: new window
ACTION: delete_group
action_type: POST
expects: POST['group_id']
neither: delete_confirm or cancel pushed
*/


/* Mark a worktype as INACTIVE.  Does not actually delete it.
Worktypes are kept for hours reporting purposes.  It does rename the old worktype.
Renamed worktype is of the form: worktype_upto_mm/yy
call from: new window
ACTION: delete_worktype
action_type: POST
expects: POST['worktype_id']

delete_confirm was pushed	
*/

function delete_worktype() 
{
	echo "<div id='action_box'><div class='box_title'>Worktype Deleted</div>";
	$_ESCAPED_WORKTYPE_ID = mysql_real_escape_string($_POST['worktype_id']);
	include 'includes/db_info.php';

	$query = "select work_name,group_name from ".$dbname.".worktype,".$dbname.".groups where
		worktype.work_id = ".$_ESCAPED_WORKTYPE_ID." AND 
		worktype.group_id = groups.group_id";
	$result = mysql_query($query);
	
	if ($row = mysql_fetch_array($result)) {
		$group_name = $row['group_name'];
		$work_name = $row['work_name'];
	}

	$query = "update ".$dbname.".worktype set inactive = 1,work_name='".$work_name." upto ".date("m/y")."' where work_id = ".$_ESCAPED_WORKTYPE_ID;
	$result = mysql_query($query,$_SESSION['dbconn']) || die ("mysql error: ".mysql_error());

	echo "Worktype: ".$work_name." in the ".$group_name." group has been made inactive.";
	echo "This worktype will still be in the database as:<BR> ".$work_name." upto ".date("m/y")."<BR>";
	echo "It will not be deleted completely for the sake of hours reporting, but in all other aspects should be considered gone for good.";
	echo "</div>";
	refresh_parent();
}

?>
