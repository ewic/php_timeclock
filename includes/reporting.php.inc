<?php

/* THIS IS AN UGLY MESS. */


function hours_report($gid,$uid,$sd,$ed) {
	$gid = mysql_real_escape_string($gid);
	$uid = mysql_real_escape_string($uid);
	$sd = mysql_real_escape_string($sd);
	$ed = mysql_real_escape_string($ed);
	$report_results = doreport($gid,$uid,$sd,$ed);
	
	$index = 0;
	$group_last = 0;
	$user_last = 0;	
	$group = "";
	$user = "";
	$user_detail = "<tr><td colspan=3><table class='user_detail' id='detail_$index'>";
	$total_hours = 0;
	$last_group_hours = 0;
	$last_user_hours = 0;
	$index = 0;
 	$o = "<table style='text-align: right;'>";
	while ($row = mysql_fetch_array($report_results))
	{
		if ($gid > 0){ 
			$group_id = $row['work_id'];
			$group_name = $row['group_name']." - ".$row['work_name'];
		}
		else {
			$group_id = $row['group_id'];
			$group_name = $row['group_name'];
		}

		if ($group_id != $group_last)
		{ 
			if ($group_last) 
			{ 
				$index++;
				$group .= "<td>".$last_group_hours."</td></tr>";
				$user .= "<td>".$last_user_hours."</td></tr>";
				
				$o .= $group;
			
				$user_detail .= "</table></td></tr>";
				$user .= $user_detail;
				$o .= $user;
				$user = "";
				$group = "";
				$user_detail = "<tr><td colspan=3><table class='user_detail' id='detail_$index'>";
				
				$user_last = 0;	
				$last_user_hours = 0;
				$last_group_hours = 0;
			}
			$group = "<tr class='heading'><td colspan=2 style='text-align: left;'>$group_name</td>";
			$group_last = $group_id;
		}
		if ($row['user_id'] != $user_last)
		{
			if ($user_last) 
			{ 
				$index++;
				$user .= "<td>".$last_user_hours."</td></tr>";
				$user_detail .= "</table></td></tr>";
				$user .= $user_detail;
				$user_detail = "<tr><td colspan=3><table class='user_detail' id='detail_$index'>";
				$last_user_hours = 0;
			}
			$user_last = $row['user_id'];
//			$color = get_user_color($row['user_id']);
			$color = "#000000";
			$user .= "<tr style='color: $color;'><td style='width: 12%; text-align: center;'><a href=\"javascript:toggle_vis('detail_$index');\" class='plus'>+</a></td><td style=\"text-align: left;\">" . $row['lname'] . ", " . $row['fname']."</td>";
		}
		$user_detail .= "<tr><td style='text-align: left; width: 80px;'>".date("m/d/y",strtotime($row['datestamp']))."</td>";
		$user_detail .= "<td style='text-align: left; width: 160px;'>".$row['work_name']."</td>";
		$user_detail .= "<td style='text-align: right; width: 40px;'>".$row['hours']."</td></tr>";
		$total_hours += $row['hours'];
		$last_group_hours += $row['hours'];
		$last_user_hours += $row['hours'];
	}
	if (!$total_hours) 
	{ 
		$o .= "<tr><td>There are no recorded hours that meet your search criteria.<BR> Please be sure the dates are correct.<BR>";
		if (check_supervisor() && $uid != '' && $gid != '')
		{ 
			$o .= "<BR> Also, make sure that the user selected belongs to the group specified."; 
		}
		$o .= "</td></tr>";
	}
	else 
	{
		$group .= "<td>".$last_group_hours."</td></tr>";
		$user .= "<td>".$last_user_hours."</td></tr>";
		$o .= $group;
		$user_detail .= "</table></td></tr>";
		$user .= $user_detail;
		$o .= $user;
		$o .= "<tr class='heading'><td colspan=2>Total:</td><td>".$total_hours."</td></tr>";
	}
	$o .= "</table>";
	return $o;

}

function doreport($group_id,$user_id,$sdate,$edate)
{
	
	$sdate = date("Y-m-d",strtotime($sdate));
	$edate = date("Y-m-d",strtotime($edate));

	$query = "select distinct hours.hours_id,groups.group_name,groups.group_id,users.username,
		users.lname,users.fname,users.user_id,hours.datestamp,hours.hours,worktype.work_name,
		worktype.work_id from groups,users,hours,worktype,group_relate
		where (";
	
	if ($group_id != '') { 
		if ($group_id < 0) { $query .= "groups.group_id = ".-$group_id." and "; }
		if ($group_id > 0) { $query .= "worktype.work_id = ".$group_id." and ";}
	}
	$query .= "(((";

	$groups = get_admin_groups($_SESSION['user_id']);	
	while ($row = mysql_fetch_array($groups))
	{
		$query .= "groups.group_id = ".$row['group_id']." or ";
	}
	$query .= "groups.group_id = 0)";
	$query .= ") or users.user_id = ".$_SESSION['user_id'].") and ";
	if (!check_supervisor()) 
	{ 
		$query .= "users.user_id = ".$_SESSION['user_id']." and "; 
	}
	else	// User belongs to group I admin or user = me
	{
		if ($user_id != '') { $query .= "users.user_id = ".$user_id." and "; }

	}
	$query .= "users.user_id = hours.user_id and hours.work_id = worktype.work_id and worktype.group_id = groups.group_id and ";
	$query .= "hours.datestamp between '".$sdate."' and '".$edate."') "; 
	if ($group_id < 0) { $query .= "order by users.lname,users.fname,hours.datestamp"; }
	elseif ($group_id > 0) { $query .= "order by users.lname,users.fname,hours.datestamp"; }
	else { $query .= "order by groups.group_name,users.lname,users.fname,hours.datestamp";}
	$result = mysql_query($query,$_SESSION['dbconn']);
	return $result;
	
}

?>
