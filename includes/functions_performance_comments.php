<?php 

/* Small box of the recently recorded hours (As of now, this is hours in the current pay period) */



function mini_comments() {
	$o = "<div class='heading'>Performance Comments</div>";
	$o .= "<div class='divider'></div><div class='box'>";
	$o .="<div id = 'mini_comments'>";
	$o .= draw_mini_comments();
	$o .= "</div></div>";
	echo $o;
}


// must return output for it to be in correct div box
function draw_mini_comments() {

    $user_info = get_user_information($_SESSION['user_id']);
    $username = $user_info['username'];
    
    //Query grabs comments made in last 7 days
    $sqlquery = " SELECT * FROM `performance_comments` WHERE username ='$username' AND TIMESTAMPDIFF(DAY , `timestamp` , NOW( ) ) < '7'";
    $result = mysql_query($sqlquery) or die("Select failed: " . mysql_error() . " on query: " . $sqlquery) ;
    $o .= "<ul>";
    while($data=mysql_fetch_array($result))
    {
        $o .= "<li>" . $data['username_commenter'] . " - " . $data['comment'] . "</li>";
    }
    $o .= "</ul>";
    return $o;
}


?>
