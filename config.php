<?php
/*
This is the main configuration file for the.system.

This file contains all of the information needed to make
the.system work, including database files, the names of the points menu,
the names of the roles and so on.

Any modifications or additions can be made here as well.

*/

//Database Information
$dbhost = "127.0.0.1";
$dbuser = "dbsource";
$dbpass = "g3n3ralapps";
$dbname = "thesystem";

/* POINTS MENU
	Edit this array to control how many items to include
	in the points menu (edit_points.php)
*/

$points_menu = array(
	'clean',
	'callbacks',
	'voicemail',
	'ssap',
	'email',
	'thumbs up',
);

/* POINTS BRACKETS
	Ths array is used when drawing the points board.

*/

$points_brackets = array(
	100,
	200,
	300,
	400,
	500,
);

/* ROLES
	Edit this array to define the names of the roles 
*/
$roles = array(
	'consultant',
	'team leader',
	'staff member',
);

/* QUEST TYPES
	Edit this array to define the types of quests
*/

$appt_types = array(
	'quest',
	'diagnostic',
	'virus clinic',
);

$buildings = array(
	'Rodman', 
	'Some Other Place',
);
//SET TO TRUE TO DISPLAY DEBUGGING INFO
$debug = false;

//console array holds debug info to print at the bottom
$console = array();

if ($debug==TRUE) {
error_reporting(E_ALL);
ini_set('display_errors', 1);
}

?>

