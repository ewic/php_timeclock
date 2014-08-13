<?php

function to_time($i) {
	$partofday = "AM";
	if ($i > 23)
  	{	
		$time1 = $i - 24;
    		$partofday="PM";
	  	if ($i > 47) { $partofday="AM"; }
	}
	else {$time1 = $i; }
	if ($time1 <2) {$time1 = 24+$time1; }
  	if ($time1 %2 !=0) { $time2 = floor($time1/2).":30 ".$partofday;}
	else { $time2 = floor($time1/2).":00 ".$partofday;}
  	return $time2;
}

function to_smalltime($i) {
  if ($i > 25)
   {$disptime1=$i-24;}
  else {$disptime1=$i;}
  if ($disptime1 %2 !=0)
   { $disptime2 = floor($disptime1/2).":30";}
  else 
   { $disptime2 = floor($disptime1/2).":00";}
   return $disptime2;
}

function to_tinytime($i) {
  if ($i > 25)
   {$disptime1=$i-24;}
  else {$disptime1=$i;}
  if ($disptime1 %2 !=0)
   { $disptime2 = floor($disptime1/2).".5";}
  else 
   { $disptime2 = floor($disptime1/2);}
   return $disptime2;
}

?>



