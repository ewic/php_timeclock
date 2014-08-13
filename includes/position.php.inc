<?php 
function selectposition($admin) {
	$o = "<OPTION disabled selected>Select position</OPTION>";
	$result = get_admin_groups($_SESSION['user_id']);
	while ($row = mysql_fetch_array($result))
		{ $admin_groups[$row['group_id']] = 1; }
	$result = get_positions($_SESSION['user_id']);
	while ($row = mysql_fetch_array($result))
		{ $positions[$row['position_id']] = 1; }

	$query = "select * from positions,groups,group_relate where 
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
                        $o .=  "<option disabled ";
                        $o .=  " style='font-weight: bold; color: #000000;'>";
                        $o .=  $row['group_name']."</option>";
                }

		$o .=  "<OPTION value=".$row['position_id'];
		$o .=  "> &nbsp ".$row['position_name']."</OPTION>";
	        $last_group = $row['group_name'];
		}
	}
	return $o;
}

function get_pos_info($id) {
	$query = "select starttime,endtime,sday,eday from positions
                 where position_id=".$id;
        $result = mysql_query($query) or die("Error:".mysql_error());
        if ($row = mysql_fetch_array($result))
        {
                $return{"stime"} = $row['starttime'];
                $return{"etime"} = $row['endtime'];
                $return{"sday"} = $row['sday'];
                $return{"eday"} = $row['eday'];
        }
	return $return;
}


?>
