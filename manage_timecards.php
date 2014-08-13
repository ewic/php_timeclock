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

open_page("Timecards");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
?>
	<div class="container">
		<?php open_panel('userlist','Users'); ?>
			<?php draw_userlist(); ?>
		<?php close_panel(); ?>
	</div>
<?
 } 

 function draw_userlist() {
 	$supers = get_users('super');
 	$users = get_users('user');
 	?>
 	<table class="userlist table table-condensed">
        <tr>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Username</th>
        </tr>
        <?php
        while ($row = $supers->fetch_array()) {
          $row['role'] = 'super';

          echo '<tr class="userlist ';
          echo 'userlist-'.$row['role'];
          if ($row['inactive'])
            echo ' userlist-inactive';
          echo '" onclick="openTimecard('.$row['user_id'].')">';
          echo '<td>'.$row['lname']."</td>";
          echo '<td>'.$row['fname']."</td>";
          echo '<td>'.$row['username']."</td>";
          echo '</tr>';
        } 

        while ($row = $users->fetch_array()) {
          $row['role'] = 'user';

          echo '<tr class="userlist ';
          echo 'userlist-'.$row['role'];
          if ($row['inactive'])
            echo ' userlist-inactive';
          echo '" onclick="openTimecard('.$row['user_id'].')">';
          echo '<td>'.$row['lname']."</td>";
          echo '<td>'.$row['fname']."</td>";
          echo '<td>'.$row['username']."</td>";
          echo '</tr>';
        } 
        ?>
      </table>
      <?php
 } 

 ?>

<!-- popup javascript -->
<script type="text/javascript"> 
function openTimecard(id) {
  window.open('timecard.php?user_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
</script>
