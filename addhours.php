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

//Pre-page logic goes here:
if (isset($_POST['addhours-submit']))
  add_hours($_POST['user_id'],$_SESSION['user_id'],$_POST['amount'],$_POST['pp']);
if (isset($_GET['pp']))
	$pp = $_GET['pp'];
else
	$pp = current_payperiod();

open_page("Add Hours");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
?>
	<div class="container">
		<?php open_panel('userlist','User List');
			draw_userlist();
			close_panel();

			open_panel('addhours','Add Hours'); ?>
			<div class="col-md-6">
			<?php draw_add_hours_form(); ?>
			</div>
			<?php close_panel(); ?>
	</div>
<?
 }

function draw_userlist() {
	global $pp;
 	$supers = get_users('super');
 	$users = get_users('user');
 	?>
 	<div class="col-md-10">
 	<table class="userlist table table-condensed">
        <tr>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Username</th>
          <th>Additional Hours</th>
        </tr>
        <?php
        while ($row = $supers->fetch_array()) {
          $row['role'] = 'super';
          $row['hours']=get_additional_hours($row['user_id'],$pp);

          echo '<tr class="userlist ';
          echo 'userlist-'.$row['role'];
          if ($row['inactive'])
            echo ' userlist-inactive';
          echo '" onclick="openAddhours('.$row['user_id'].')">';
          echo '<td>'.$row['lname']."</td>";
          echo '<td>'.$row['fname']."</td>";
          echo '<td>'.$row['username']."</td>";
          echo '<td>'.$row['hours'].'</td>';
          echo '</tr>';
        } 

        while ($row = $users->fetch_array()) {
          $row['role'] = 'user';
          $row['hours']=get_additional_hours($row['user_id'],$pp);

          echo '<tr class="userlist ';
          echo 'userlist-'.$row['role'];
          if ($row['inactive'])
            echo ' userlist-inactive';
          echo '" onclick="openAddhours('.$row['user_id'].')">';
          echo '<td>'.$row['lname']."</td>";
          echo '<td>'.$row['fname']."</td>";
          echo '<td>'.$row['username']."</td>";
          echo '<td>'.$row['hours'].'</td>';
          echo '</tr>';
        } 
        ?>
      </table>
      </div>
      <div class="col-md-2">
      	<form class="form form-horizontal" action="">
	 	<label for="pp">Pay Period: </label>
    <select name="pp" class="form-control" onchange="this.form.submit()">
	 	<?php
    $number_of_payperiods = get_number_of_payperiods();
	 	for($i=1;$i<=$number_of_payperiods;$i++) {
	 		echo '<option value="'.$i.'"';
			if ($pp == $i)
	 			echo ' selected="selected" ';
	 		echo '>'.$i.'</div>';
	 	}
	 	?>
	 	</select>
	 	</form>
      </div>
      <?php
} 

function draw_add_hours_form() {
  global $pp;
  
	$users = get_users('user');
  $supers = get_users('super');
	?>
	<form role="form" class="form" method="post" action="">
  <div class="col-sm-12">
  <label for="user_id">User: </label>
	<select class="form-control" name="user_id">
		<?php
		while ($super = $supers->fetch_array()) {
      echo '<option value='.$super['user_id'].'>';
      echo $super['lname'].', '.$super['fname'];
      echo '</option>';
    }

    while ($user = $users->fetch_array()) {
			echo '<option value='.$user['user_id'].'>';
			echo $user['lname'].' '.$user['fname'];
			echo '</option>';
		}
		?>
	</select>
  </div>
  <div class="col-sm-6">
  <label for="pp">Pay Period: </label>
    <select name="pp" class="form-control">
    <?php
    $number_of_payperiods = get_number_of_payperiods();
    for($i=1;$i<=$number_of_payperiods;$i++) {
      echo '<option value="'.$i.'"';
      if ($pp == $i)
        echo ' selected="selected" ';
      echo '>'.$i.'</div>';
    }
    ?>
    </select>
    </div>
    <div class="col-sm-6">
  <label for="amount">Amount: </label>
    <select name="amount" class="form-control">
    <?php
    for($i=1;$i<=10;$i++)
      echo '<option value="'.$i.'">'.$i.'</option>';
    ?>
    </select>
    </div>
    <div class="pull-right">
      <input type="hidden" name="addhours-submit" value="true">
      <button class='btn btn-default' onclick="submit()">Submit</button>
    </div>
	</form>
	<?php
}

?>

<!-- popup javascript -->
<script type="text/javascript">
function openAddhours(id) {
  window.open('additionalhours.php?user_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
</script>