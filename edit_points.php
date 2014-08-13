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
if (isset($_POST['addpoints-submit']))
  add_points(strtolower($_POST['note']),$_POST['value'],$_GET['user_id']);

if (isset($_POST['removepoints-submit']))
  remove_points($_POST['point_id']);

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
  ?>
  <div class="container">
  <?php
  //Fetch the points total for this user
  $points = get_points_total($userinfo['user_id']);
  echo '<h3>';
  echo $userinfo['fname'].' '.$userinfo['lname'];
  echo '&nbsp;<small>'.$points.' points</small>';
  echo '</h3>';

  draw_points_table($userinfo);
  draw_add_points_form();

  ?>
  </div>
  <?php
} 

function draw_points_table($userinfo) {
  $points = get_points($userinfo['user_id']);

  echo '<table class="table table-condensed">';
  echo '<tr>';
    echo '<th>Value</th>';
    echo '<th>Note</th>';
    echo '<th>Team Leader</th>';
    echo '<th>Date</th>';
    echo '<th><span class="glyphicon glyphicon-remove-circle btn btn-default btn-progress"></span></th>';
  echo '</tr>';

  while ($point = $points->fetch_array()) {
    //Open with a form, to submit for the removal of this record.
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="removepoints-submit" value="true">';
    echo '<input type="hidden" name="point_id" value="'.$point['point_id'].'">';

    echo '<tr>';
    echo '<td>'.$point['value'].'</td>';
    echo '<td>'.ucfirst($point['note']).'</td>';
    echo '<td>'.$point['tl_fname'].', '.$point['tl_lname'].'</td>';
    //convert the timestamp to a YYYY-MM-DD date
    echo '<td>'.date('Y-m-d',strtotime($point['timestamp'])).'</td>';
    echo '<td><button class="btn btn-default btn-progress" onclick="submit()"><span class="glyphicon glyphicon-remove-circle"></span></button></td>'; //Here's the submit button to remove this record
    echo '</tr>';

    echo '</form>'; //Close the form we opened above.
  }

  echo '</table>';
}

function draw_add_points_form() {
  global $points_menu;
  ?>
  <!-- First form- -->
  <form role="form" action="" method="post" class="form form-horizontal">
  
  <div class="row">
  <div class="col-xs-9">
  <label for="note">Add points</label>
    <select id="note" name="note" class="form-control">
      <?php
      foreach ($points_menu as $menu_item) {
        echo '<option>'.ucfirst($menu_item).'</option>';
      }
      ?>
    </select>
    </div>
  <div class="col-xs-3">
  <label for="value">Value</label>
    <input type="text" name="value" id="value" value=5 class="form-control">
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 pull-right">
    <input type="hidden" name="addpoints-submit" value="true">
    <button class="btn btn-default btn-sm pull-right" onclick="submit()">Submit</button>
  </div>
  </div>
  </form>

  <!-- Second form -->
  <form role="form" action="" method="post" class="form form-horizontal">
  
  <div class="row">
  <div class="col-xs-9">
  <label for="customnote">Custom Label</label>
    <input type="text" id="customnote" name="note" class="form-control">
  </div>

  <div class="col-xs-3">
  <label for="customvalue">Value</label>
    <input type="text" id="customvalue" name="value" value=5 class="form-control">
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 pull-right">
    <input type="hidden" name="addpoints-submit" value="true">
    <button class="btn btn-default btn-sm pull-right" onclick="submit()">Submit</button>
  </div>
  </div>
  </form>

  <?php
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

 window.opener.location.reload(false);
 </script>

