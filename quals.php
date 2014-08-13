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

open_page("Quals Progress");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
?>
	<div class="container">
		<?php open_panel('quals','Quals'); 
		draw_user_list();
		close_panel(); ?>
	</div>
<?
 } 

function draw_user_list() {
  $users = get_users("user");
  $total = get_qual_point_total();

  ?>
      <table class="userlist table table-condensed">
        <tr>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Username</th>
          <th>Progress</th>
        </tr>
        <?php
        while ($row = $users->fetch_array()) {
          $row['role'] = get_role($row['user_id']);
      	  $progress = get_progress($row['user_id']);

          echo '<tr class="userlist ';
          echo 'userlist-'.$row['role'];
          if ($row['inactive'])
            echo ' userlist-inactive';
          echo '" onclick="openProgress('.$row['user_id'].')">';
          echo '<td>'.$row['lname']."</td>";
          echo '<td>'.$row['fname']."</td>";
          echo '<td>'.$row['username']."</td>";
          echo '<td>';
          draw_qual_progress_bar($progress,$total);
          echo '</td>';
          echo '</tr>';
        } 
        ?>
      </table>
  <?php
}

function draw_qual_progress_bar($progress,$total) {
  //Avoid division by 0.
  if ($total ==0)
    $total = 1;
  $percent = round(($progress/$total)*100);
  echo '<div class="progress qual-progress-bar">';
  echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">';
  echo '<span>'.$progress.'/'.$total.'</span>';
  echo '</div>';
  echo '</div>';
}

 ?>

<!-- popup javascript -->
<script type="text/javascript"> 
function openProgress(id) {
  window.open('edit_progress.php?user_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
</script>