<?
function mini_print_announcements()
{
// format the announcement list in pretty divs
        $o  = "<div class='heading'>Announcements</div>";
        $o .= "<div class='divider'></div><div class='box'>";
        $o .= "<div id='announcements'>";
        $o .= get_announcements();
        $o .= "</div>";
        $o .= "</div>";
        echo $o;
}

function get_announcements()
// return a list of all the announcements from my groups
{

	$o = "";
	$query = "select announcement from announcements where start_date <= now() and adddate(end_date, interval 1 day) >= now() and active = true ";
	$query .= "and group_id in (select distinct group_id from group_relate where user_id = " . $_SESSION['user_id'] . ");";
	$result = mysql_query($query) or die("Could not get announcements");

	if(mysql_num_rows($result) > 0)
	{ 
		$o .= "<ul>";
		while($row = mysql_fetch_array($result))
		{
			$o .= "<li>" . stripslashes($row['announcement']) . "</li>";
		}
		$o .= "</ul>";
	}
    else
	{ $o .= "No current announcements"; }

	return $o;
}
?>
