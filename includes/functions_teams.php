<?php
//Adds a user to another user's team.
function add_to_team($user_id,$tl_id) {

    //Make sure the user is not a team leader
    if (check_supervisor($user_id))
        $_SESSION['notifications'][] = "That user is a team leader!";

    //If not, do things.
    else {
        //If he's already part of a team, drop him from that team.
        if (!is_orphan($user_id))
            remove_from_team($user_id);
        $sql = "INSERT INTO teams (tl_id,user_id) VALUES (".$tl_id.','.$user_id.")";
        $_SESSION['dbconn']->query($sql) or die("Error adding to team: ".$_SESSION['dbconn']->error);
        $_SESSION['notifications'][] = "Added user to team";
    }
}

//Removes all team membership for a user.
function remove_from_team($user_id) {
    $sql = "DELETE FROM teams WHERE user_id=".$user_id;
    $_SESSION['dbconn']->query($sql) or die("Error removing from team: ".$_SESSION['dbconn']->error);
    $_SESSION['notifications'][] = "Removed from team";
}

//Returns true if the user does not have a team leader
function is_orphan($user_id) {
    $sql = "SELECT user_id FROM teams WHERE user_id=".$user_id;
    $result = $_SESSION['dbconn']->query($sql) or die("Error checking orphan.");
    if ($result->num_rows==0)
        return true;
    else
        return false;
}

//TODO Return the team leader's id if success, return false if failure
function get_team_leader($user_id=NULL) {
    if (!$user_id)
        $user_id = $_SESSION['user_id'];

    //Check to make sure that you're not a tl
    if (check_supervisor($user_id))
        return $user_id;

    //Also, if you're an admin...
    if (check_app_admin($user_id))
        return $user_id;
    
    else {
        $sql = "SELECT tl_id FROM teams WHERE user_id=".$user_id;
        $result = $_SESSION['dbconn']->query($sql) or die("Error checking team membership");

        if ($result->num_rows!=0) {
            $out = $result->fetch_array();
            return $out[0];
        }
        else
            return false;
    }
}

//Returns a list of all people who are not in a team, but also not app admins or supers.
function get_orphans() {
    $sql = "SELECT user_id,fname,lname FROM users WHERE user_id NOT IN (SELECT user_id FROM teams) AND user_id NOT IN (SELECT * FROM app_admin) AND user_id NOT IN (SELECT * FROM app_supervisor) AND inactive=0"; 
    $result = $_SESSION['dbconn']->query($sql);
    return $result;
}

//Returns a list of all the team leaders;
function get_team_leaders() {
    return get_users("super");
}

//Returns a list of all the teams members in a team
function get_team_members($tl_id) {
    if (!check_supervisor($tl_id))
        return FALSE;
    else {
       $sql = "SELECT user_id,username,fname,lname FROM users WHERE user_id IN (SELECT user_id FROM teams WHERE tl_id=".$tl_id.")";
       //echo $sql;
       $result = $_SESSION['dbconn']->query($sql);
       return $result;
    }
}
?>