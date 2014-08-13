<?php

/*  Authentication function takes a username and password and checks them against
    database.
*/

function authenticate($type) {
	//$authentication_type{"ldap"} = array("function" => "ldap_authenticate");
	//$authentication_type{"password"} = array("function" => "authenticate_password");
	//return $authentication_type{$type}{"function"};
    return "authenticate_password";
}

/* Validation function always takes a username and password
   1.) Check to see if the user is in this APPs database
	user_id = -1 if not
	user_id = positive real user_id if so
   2.) Get the authentication function for this user
   3.) Authenticate (via ldap, password, etc... by using authentication function)
   4.) Set session variables (validated, user_id)
*/

function validate($username,$password)
{
	$exists = dblookupuser($username);
	$active = is_active($username);
	if ($exists != -1 && $active)
	{
		$authenticate = "authenticate_password";
		 if ($authenticate($username,$password))
			{
				$_SESSION['validated'] = true;
				$_SESSION['user_id'] = $exists;
				$_SESSION['notifications'] = array();
				log_attempt($username,true);
				return true;
			}
	}
	log_attempt($username,false);
	return false;
}

/* Check to see if the username supplied is a valid user in this application
   This function set's the session variable authtype for use in the validate function 
   returns -1 if user is not valid
   returns real user id if valid.
*/

function dblookupuser($username)
{
	$_ESCAPED_username=$_SESSION['dbconn']->real_escape_string($username);
	$query = "SELECT user_id FROM users WHERE username = '".$_ESCAPED_username."'";
	$result = $_SESSION['dbconn']->query($query) or die("Query failed: ".$_SESSION['dbconn']->error);
	if ($rowresult = $result->fetch_array())
	{
		return $rowresult['user_id'];

	}
	else
	{ return -1; }
}

/* Used for authenticating via password within this application's db
   This app uses the MD5 sum of passwords
*/

function authenticate_password($username,$password)
{
	$_ESCAPED_username=$_SESSION['dbconn']->real_escape_string($username);
	$_MD5_password = md5($password);
	$query = "select password from users where username = '".$_ESCAPED_username."'";
	$result = $_SESSION['dbconn']->query($query) or die("Query failed: ".mysql_error());
	if ($rowresult = $result->fetch_array())
	{ 
		if ($_MD5_password == $rowresult['password'])	{ return true; }
		else { return false; }
	}
	return false;	
}

function is_active($username)
{
	$query = "SELECT inactive FROM users WHERE username = '" . $username . "' limit 1;";
	$result = $_SESSION['dbconn']->query($query) or die("mysql could not find whether user is active");
	$row = $result->fetch_array();
	if($row['inactive'] == 0)
	{ return true; } else { return false; }
}

function log_attempt($username,$result)
// logs the username, IP address, and result of each login attempt
{
	$ip_address = $_SESSION['dbconn']->real_escape_string(strip_tags($_SERVER['REMOTE_ADDR']));
	$username = $_SESSION['dbconn']->real_escape_string(strip_tags($username));
	$page =  $_SESSION['dbconn']->real_escape_string(strip_tags($_SERVER['PHP_SELF']));

	$query = "INSERT INTO login_attempts (username,ip_address,page,successful) VALUES ('$username','$ip_address','$page','$result');";
	$_SESSION['dbconn']->query($query);
}
?>
