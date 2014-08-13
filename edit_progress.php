<?php 
ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();
require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes");

mysql_init();

document_header();
echo include_javascript_dir("js",$debug);
echo include_stylesheet_dir("stylesheets",$debug);

check_validated();

//Any pre-page logic should go here!
if (isset($_POST['finishqual-submit']))
	finish_qual($_POST['qual_id'],$_GET['user_id']);

if (isset($_POST['unfinish-submit']))
	unfinish_qual($_POST['qual_id'],$_GET['user_id']);

if (count($_SESSION['notifications'])!=0)
  draw_notification();

if (isset($_GET['user_id']))
  $userinfo = get_user_info($_GET['user_id']);


draw_page($userinfo);
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

function draw_page($userinfo) 
{	
  	$progress = get_progress($userinfo['user_id']);
  	$total = get_qual_point_total();
	?>
	<div class="container">
	<?php
	  echo '<h3>';
	  echo $userinfo['fname'].' '.$userinfo['lname'];
  	  echo '&nbsp;<small>'.user_level_to_levelname(get_level($userinfo['user_id'])).'</small>';

	  echo '</h3>';

	  draw_qual_progress_bar($progress,$total);

	  draw_progress_table($userinfo);

	  draw_qual_progress_form($userinfo);
	  ?>
  </div>
 <?php
} 

function draw_progress_table($userinfo) {
	$finished_quals = get_qual_progress($userinfo['user_id']);
	
	$total=0;

	echo '<table class="table table-condensed">';
	echo '<tr>';
		echo '<th>Level</th>';
		echo '<th>Qual</th>';
		echo '<th>Points</th>';
		echo '<th>Team Leader</th>';
		echo '<th>Date</th>';
		echo '<th><span class="glyphicon glyphicon-remove-circle btn btn-default btn-progress"></span></th>';
	echo '</tr>';

	while ($qual = $finished_quals->fetch_array()) {
		$total += $qual['value'];

		echo '<form method="post">';
		echo '<input type="hidden" name="unfinish-submit" value="true">';
		echo '<input type="hidden" name="qual_id" value="'.$qual['qual_id'].'">';

		echo '<tr>';
		echo '<td>'.ucfirst(qual_level_to_levelname($qual['level'])).'</td>';
		echo '<td>'.$qual['qualname'].'</td>';
		echo '<td>'.$qual['value'].'</td>';
		echo '<td>'.$qual['tl_fname'].', '.$qual['tl_lname'].'</td>';
		echo '<td>'.$qual['completion_date'].'</td>';
		echo '<td><button class="btn btn-default btn-progress" onclick="submit()"><span class="glyphicon glyphicon-remove-circle"></span></button></td>';
		echo '</tr>';

		echo '</form>';
	}
	//Empty td's for cell spacing.
	echo '<td></td><td><b>Total</b></td>';
	echo '<td>'.$total.'</td>';
	echo '<td></td><td></td><td></td>';
	echo '</table>';
}

function draw_qual_progress_form($userinfo) {
	$not_done_quals = get_not_done_quals($userinfo['user_id']);
	echo '<form action="" method="post">';
	echo '<div class="col-xs-10">'; //START col-xs-10
	echo '<select class="form-control" name="qual_id">';
	  while ($qual = $not_done_quals->fetch_array()) {
	  	echo '<option value="'.$qual['qual_id'].'">';
	  	echo ucfirst(qual_level_to_levelname($qual['level'])).' - '.$qual['name'];
	  	echo '</option>';
	  }
  	echo '</select>';
  	echo '</div>'; //END col-xs-10
  	echo '<input type="hidden" name="finishqual-submit" value="true">';
  	echo '<div class="col-xs-2">'; //START col-xs-2
  	echo '<input type="submit" class="btn btn-default">';
  	echo '</div>'; //END col-xs-2
  	echo '</form>';
}

function draw_qual_progress_bar($progress,$total) {
  //Avoid division by 0 issues.
  if ($total == 0)
  	$total = 1;
  $percent = round(($progress/$total)*100);
  echo '<div class="progress qual-progress-bar">';
  echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">';
  echo '<span>'.$progress.'/'.$total.'</span>';
  echo '</div>';
  echo '</div>';
}

 ?>

<!-- Closes the window on esc keypress. -->
<script>
$(document).keydown(function(e) {
    // ESCAPE key pressed
    if (e.keyCode == 27) {
        window.close();
    }
});

//Every time this window is updated, refresh the parent window.  Keeps the windows in sync with each other.
 window.opener.location.reload(false);
 </script>