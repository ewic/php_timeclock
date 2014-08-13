<?
//  This function should be included at the beginning of each of the php pages. (almost all of them)

function get_mysql_info() {
	include "db_info.php";
	$result = array("db" => $dbname,
			"host" => $server,
			"username" => $dbusername,
			"password" => $dbpasswd);
	return $result;
}


function mysql_init()
{
	global $dbuser,$dbpass,$dbname,$dbhost;
	$_SESSION['dbconn'] = new mysqli($dbhost,$dbuser,$dbpass,$dbname);

	if($_SESSION['dbconn']->connect_errno > 0)
		die('Could not connect: '.$_SESSION['dbconn']->connect_error);
	
}

function mysql_end()
{
	if (isset($_SESSION['dbconn']))
		$_SESSION['dbconn']->close();
}
// TWO functions for compatibility only

?>
