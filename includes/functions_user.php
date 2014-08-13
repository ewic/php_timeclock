<?php

function check_app_admin($user_id=NULL)
{
    if(!$user_id)
        $user_id = $_SESSION['user_id'];

    $query = "SELECT user_id FROM app_admin WHERE user_id = ".$user_id;
    $result = $_SESSION['dbconn']->query($query) or die ("Error checking for application admin with user_id: ".$user_id.": ".$_SESSION['dbconn']->error);
    if ($result->num_rows)
        return true;
    else
        return false;
}

function check_supervisor($user_id=NULL)
{
    if(!$user_id)
        $user_id = $_SESSION['user_id'];

    $query = "SELECT user_id FROM app_supervisor WHERE user_id = '".$user_id."'";
    $result = $_SESSION['dbconn']->query($query);
    if ($result->num_rows || check_app_admin($user_id)) 
        return true;
    else
        return false;
}

function get_role($user_id=NULL) {
    if (!$user_id)
        $user_id = $_SESSION['user_id'];
    if (check_app_admin($user_id))
        return "admin";
    else if (check_supervisor($user_id))
        return "super";
    else 
        return "user";
}

function get_admin_ids()
{
    if (!check_app_admin())
        return "You are not admin!";
    else {
        $sql = "SELECT user_id FROM app_admin";
        $result = $_SESSION['dbconn']->query($sql) or die("Error getting app admins: ".$_SESSION['dbconn']->error);
        return $result->fetch_array();
    }
}

//Returns a list of users.  If $role is admin, return a list of admin users, if $role is super, return a list of supervisors.
function get_users($role=NULL,$inactive=false)
{
    $query = "SELECT user_id,username,fname,lname,inactive FROM users ";

    //Now, if we want to exclude other users, do this thing.
    if ($role=="admin")
        $query .= "WHERE user_id IN (SELECT user_id FROM app_admin)";
    else if ($role == "super")
        $query .= "WHERE user_id IN (SELECT user_id FROM app_supervisor)";
    else if ($role == "user")
        $query .= "WHERE user_id NOT IN (SELECT user_id FROM app_admin UNION SELECT user_id FROM app_supervisor)"; 

    //If we want to exclude inactives, do this thing
    if ($inactive == FALSE && $role!=null)
        $query .= "AND inactive=0";
    else if ($inactive = FALSE && $role==null)
        $query .= "WHERE inactive=0";

    $query .= " ORDER BY inactive,lname,fname";
    //echo $query;
    $result = $_SESSION['dbconn']->query($query);
    return $result;
}

function get_user_information($user_id) {

    $query = "SELECT user_id,username,fname,lname,email FROM users WHERE user_id = ".$user_id;
    $result = $_SESSION['dbconn']->query($query)
    or die("Get User Info Query failed = ". $query ." = ".$_SESSION['dbconn']->error());
    if ($row = $result->fetch_array()) {
        $user_info = array(
                    "user_id" => $row['user_id'],	
                    "username"	=> 	$row['username'],
                    "fname"	=> 	$row['fname'],
                    "lname"	=> 	$row['lname'],
                    "email"	=> 	$row['email']);
    }
    return $user_info;
}

//Pass it an array of userinfo
function adduser($userinfo) 
{
    //Check the data
    $querycheck = "SELECT username,emplid FROM users WHERE (";
    $querycheck .= "username='".$userinfo['username']."'";
    $querycheck .= "OR emplid='".$userinfo['emplid']."'";
    $querycheck .= "OR email='".$userinfo['email']."'";
    $querycheck .= ")";
    $result = $_SESSION['dbconn']->query($querycheck);
    if ($result->num_rows > 0)
    {
        $_SESSION['notifications'][] = $userinfo['username']." already exists in the database!  Please make sure this user has a unique username, emplid, AND email address.";
    }
    
    else if ($userinfo['password']!=$userinfo['pwconfirm']) {
        $_SESSION['notifications'][] = "Passwords do not match!  Please reenter the password.";
    }

    else {
        $sql = "INSERT INTO users (username,fname,lname,emplid,email,phone,address,password) VALUES (";
        $sql .= "'".$userinfo['username']."', ";
        $sql .= "'".$userinfo['fname']."', ";
        $sql .= "'".$userinfo['lname']."', ";
        $sql .= "'".$userinfo['emplid']."', ";
        $sql .= "'".$userinfo['email']."', ";
        $sql .= "'".$userinfo['phone']."', ";
        $sql .= "'".$userinfo['address']."', ";
        $sql .= "MD5('".$userinfo['password']."')";
        $sql .= ")";

        echo $sql;

        $result = $_SESSION['dbconn']->query($sql) or die("Error adding user: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] = "Successfully added user, ".$userinfo['username']."!";
    }   
}

//No Deleting users, only deactivating them
function delete_user($user_id){
    if (!check_app_admin()) 
        $_SESSION['notifications'][] = "You are not an administrator!";
    else if ($user_id == $_SESSION['user_id'])
        $_SESSION['notifications'][] = "You cannot deactivate yourself!";
    else {
        if (check_app_admin($user_id))
            unmake_admin($user_id);
        if (check_supervisor($user_id))
            unmake_super($user_id);
        $sql = "UPDATE users SET inactive=1 WHERE user_id=".$user_id;
        $_SESSION['dbconn']->query($sql) or die("Error deactivating user: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] = "Successfully removed user!";
    } 
}

function undelete_user($user_id){
    if (!check_app_admin()) 
        return "You are not an administrator!";
    else {
        $sql = "UPDATE users SET inactive=0 WHERE user_id=".$user_id;
        $_SESSION['dbconn']->query($sql) or die("Error activating user: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] =  "Reactivated user!";
    } 
}

//Returns an array of userinfo
function get_user_info($user_id=NULL) {
    if (!$user_id)
        $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM users WHERE user_id=".$user_id;

    $result = $_SESSION['dbconn']->query($sql);

    return $result->fetch_assoc();
}

function update_userinfo($userinfo) {
    //$userinfo['phone'] = clean_up_phone($userinfo['phone']);
    //$userinfo = safe_array($userinfo);
    $query = "UPDATE users SET email = '" . $userinfo['email']."'";
    $query .= ", phone = '" . $userinfo['phone'] . "'";
    $query .= ", address = '" . $userinfo['address'] . "' ";
    $query .= " WHERE user_id = '" . $userinfo['user_id'] . "' limit 1";

    $_SESSION['dbconn']->query($query) or die("could not update your information: " . mysql_error());
    $_SESSION['notifications'][] = "Updated your information";
}

//Changes userinfo for edit_user.php
function edit_user($userinfo) {
    $sql = 'UPDATE users SET ';
    $sql .= 'username = "'.$userinfo['username'].'"';
    $sql .= ', fname = "'.$userinfo['fname'].'"';
    $sql .= ', lname = "'.$userinfo['lname'].'"';
    $sql .= ', emplid = "'.$userinfo['emplid'].'"';
    $sql .= ' WHERE user_id = "'.$userinfo['user_id'].'" limit 1';

    $_SESSION['dbconn']->query($sql) or die("could not update your information: " . $_SESSION['dbconn']->error);
    $_SESSION['notifications'][] = "Updated user information";
}

//Resets password for a user to a randomized string, then returns the new password.
function reset_password($user_id) {
    if(!check_app_admin())
        $_SESSION['notifications'][] = "You are not an admin!";
    else {
        $valid_chars = "abcdefghijklmnopqrstuvwxyz1234567890";
        $length = 8;
        $newpass = get_random_string($valid_chars,$length);

        $sql = "UPDATE users SET password='".md5($newpass)."' WHERE user_id='".$user_id."'";
        $result = $_SESSION['dbconn']->query($sql) or die("Error resetting password:".$_SESSION['dbconn']->error);
        return $newpass;
    }
}

//Function to produce a random string
function get_random_string($valid_chars, $length)
{
    // start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length
    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}

//Changes a password for the logged in user
function change_password($newpass) {
    $sql = "UPDATE users SET password='".md5($newpass)."' WHERE user_id=".$_SESSION['user_id'];
    $result = $_SESSION['dbconn']->query($sql) or die("Error changing password: ".$_SESSION['dbconn']->error);
    $_SESSION['notifications'][] =  "Password changed";
    return true;
}

//-- PRIVILEGES & ROLES FUNCTIONS --\\

//Adds a user to the app_admin table
function make_admin($user_id) {
    if (check_app_admin($user_id))
        $_SESSION['notifications'][] =  "Error, that user is already an admin!";
    else if (check_supervisor($user_id))
        $_SESSION['notifications'][] =  "Error, that user is a supervisor.  Please remove that user from supervisors first.";
    else {
        $sql = "INSERT INTO app_admin VALUES (".$user_id.")";
        $result = $_SESSION['dbconn']->query($sql) or die("Error making admin: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] =  "Added user to administrators";
    }
}

//Drops a user from the app_admin table
function unmake_admin($user_id) {
    if (!check_app_admin($user_id)) {
        $_SESSION['notifications'][] =  "That user is not an admin!";
    }
    else if($user_id == $_SESSION['user_id']) {
        $_SESSION['notifications'][] =  "You cannot remove yourself from administrators!";
    }
    else {
        $sql = "DELETE FROM app_admin WHERE user_id=".$user_id;
        $result = $_SESSION['dbconn']->query($sql) or die("Error removing admin: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] =  "Removed user from administrators";
    }
}

//Adds a user to the app_supervisor table
function make_super($user_id) {
    if (check_supervisor($user_id)){
        $_SESSION['notifications'][] = "Error, that user is already a supervisor!";
    }

    else if (check_app_admin($user_id))
        $_SESSION['notifications'][] = "Error, that user is an admin.  Please remove that user from admins first.";

    else {
        //First, remove the user from a team, if he's in one.
        remove_from_team($user_id);

        $sql = "INSERT INTO app_supervisor VALUES (".$user_id.")";
        $result = $_SESSION['dbconn']->query($sql) or die("Error making supervisor: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] = "Added user to supervisors";
    }
}

//Drops a user from the app_supervisor table
function unmake_super($user_id) {
    //Check to make sure the user's a supervisor
    if (!check_supervisor($user_id))
        $_SESSION['notifications'][] = "Error, user is not a supervisor!";
    
    else {
        empty_team($user_id);
        $sql = "DELETE FROM app_supervisor WHERE user_id=".$user_id;
        $result = $_SESSION['dbconn']->query($sql) or die("Error removing supervisor: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] = "Removed user from supervisors";
    }
}

//Empties a team from a team leader
function empty_team($tl_id) {
    $sql = "DELETE FROM teams WHERE tl_id=".$tl_id;
    $_SESSION['dbconn']->query($sql);
    $_SESSION['notifications'][] = "Emptied team";
}
?>
