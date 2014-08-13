<?php 

/* This page defines some constants for this application. */

/* This section might be in the wrong place.  These are constants only 
based on which position is being viewed.  If no position is being viewed 
don't try to define them.

Included here are position start day and time as well as end day and 
time.

Fieldwidth is changed based on how many hours are in the schedule.  This 
ensures that short days and long days still fill up the schedule width. */

/*if (isset($_SESSION['position_id']))
{
	include 'includes/db_info.php';
	$query = "select starttime,endtime,sday,eday from ".$dbname.".positions
                 where position_id=".$_SESSION['position_id'];
	$result = mysql_query($query);
	if ($row = mysql_fetch_array($result))
	{
      		$starttime = $row['starttime'];
      		$endtime = $row['endtime'];
      		$sday = $row['sday'];
      		$eday = $row['eday'];
	}
}
*/
/* Basic values for a consistent look */
global $fieldspacing,$timeblockheight,$timeblockspacing,$dayblockspacing,$dow,$SUNDAY;

$fieldspacing = 4;
$timeblockheight = 15;
$timeblockspacing = 5;
$dayblockspacing = 10;

/* Days of the week (the date('w') command will give a number, this 
translates those numbers to their written form.

The abbreviated dates are just that. */

$dow[0] = "Sunday";
$dow[1] = "Monday";
$dow[2] = "Tuesday";
$dow[3] = "Wednesday";
$dow[4] = "Thursday";
$dow[5] = "Friday";
$dow[6] = "Saturday";
$ab_dow[0] = "Sun.";
$ab_dow[1] = "Mon.";
$ab_dow[2] = "Tue.";
$ab_dow[3] = "Wed.";
$ab_dow[4] = "Thu.";
$ab_dow[5] = "Fri.";
$ab_dow[6] = "Sat.";

/* Sunday is the starting UNIX TIME of the current week. 
Actually, this is the UNIX time of the same time/minute/second on Sunday 
that this script is being called.  

Elsewhere floor is used to get the beginning of the Sunday time.  That 
could be changed here as well. */

$SUNDAY = time()-date("w")*86400;

?>
