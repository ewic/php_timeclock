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

open_page("Race for Excellence");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() {	
	?>
	<div class="container">
	<?php
    open_panel('teamlist','Race For Excellence',false);
    draw_team_panel();
    close_panel();

		open_panel('userlist','Points',false);
		draw_user_list();
		close_panel();
	?>
	</div>
	<?php
} 

function draw_team_panel() {
  global $points_brackets;

  $teams = get_team_leaders();
  $teampoints = array();
  while ($team = $teams->fetch_array()) {
    $teampoints[$team['user_id']]=get_team_point_total($team['user_id']);
  }
  $highest = max($teampoints);
  $bracket = 100;

  while ($bracket < $highest) {
    $bracket = current($points_brackets);
    next($points_brackets);
  }

  foreach ($teampoints as $team=>$teampoint) {
    $tlinfo = get_user_info($team);
    echo 'Team '.$tlinfo['lname'];
    draw_progress_bar($teampoint, $bracket);
  }
}

function draw_user_list() {
  $users = get_users('user');
  ?>
      <table class="userlist table table-condensed">
        <tr>
          <th>Name</th>
          <th>Points</th>
        </tr>
        <?php
        while ($row = $users->fetch_array()) {

        $points=get_points_total($row['user_id']);
          echo '<tr class="userlist"';
          echo ' onclick="openPoints('.$row['user_id'].')">';
          echo '<td>'.$row['fname'].' '.$row['lname'].'</td>';
          echo '<td>'.$points.'</td>';
          echo '</tr>';
        } 
        ?>
      </table>
  <?php
}

function draw_progress_bar($progress,$total) {
  $percent = round(($progress/$total)*100);
  ?>
  <div class="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total; ?>" style="width:<?php echo $percent; ?>%;">
      <span><?php echo $progress; ?> Points</span>
    </div>
  </div>
<?php
}

 ?>

<!-- popup javascript -->
<script type="text/javascript"> 
function openPoints(id) {
  window.open('edit_points.php?user_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
</script>