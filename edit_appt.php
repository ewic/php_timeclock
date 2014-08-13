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

//stupid datepicker
echo '<script src="http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>';

check_validated();

//Any pre-page logic should go here!
if (isset($_POST['updateAppt-submit'])) {
  $_POST['quest_id'] = $_GET['quest_id'];
  appt_edit($_POST);
}

if (isset($_POST['delete_appt-submit'])) {
  appt_delete($_GET['quest_id']);
  echo "<script>window.close();</script>";
}

if (isset($_GET['quest_id']))
  $apptinfo = get_appt_info($_GET['quest_id']);


draw_page($apptinfo);
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

function draw_page($apptinfo) 
{ 
  ?>
  <div class="container">
  <div class="col-xs-12">
    <h3>
    Edit Appointment
    <div class="pull-right">
      <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete">Delete</button>
    </div>
    </h3>
  </div>
  <?php
  draw_appt_edit_form($apptinfo);
  draw_delete_appt_modal();
  ?>
  </div>
  <?php
} 

function draw_appt_edit_form($apptinfo) {
  global $appt_types,$buildings;

  $date=date('Y-m-d', strtotime($apptinfo['datetime']));
  $time=date('H:00:00', strtotime($apptinfo['datetime']));
  ?>
  <form action='' method='post' role="form">
    <div class="form-group col-xs-6">
    <label for="date">Date</label>
      <div class="input-group date">
              <input type="text" class="form-control" name="date" value=<?php echo '"'.$date.'"'; ?>><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
      </div>
    </div>
    <div class="form-group col-xs-6">
    <label for="time">Time</label>
      <select class="form-control" name="time">
          <?php
          for ($i=10;$i<=18;$i++) {
            if ($i>12) {
              $j = $i-12;
              $ampm = 'pm';
            }
            else {
              $j = $i;
              $ampm = 'am';
            }
            echo '<option value="'.$i.'">'.$j.':00 '.$ampm.'</option>';
          }
          ?>
      </select>
    </div>

    <div class="form-group col-xs-6">
    <label for="type">Type</label>
    <select class="form-control" name="type">
      <?php
      for ($i=0;$i<count($appt_types);$i++) {
        echo '<option value='.$i;
        if ($apptinfo['type']==$i)
          echo ' selected="true"';
        echo '>'.ucfirst(appt_type_to_string($i)).'</option>';
      }
      ?>
    </select>
    </div>

    <div class="form-group col-xs-6">
    <label for="tt">Trouble Ticket</label>
    <input type="text" class="form-control" name="tt" value=<?php echo $apptinfo['tt']; ?>>
    </div>

    <div class="form-group col-xs-6">
    <label for="building">Building</label>
    <select class="form-control" name="building">
      <?php
      foreach ($buildings as $building) {
        echo '<option value="'.strtolower(str_replace(' ', '_', $building)).'"';
        if (strtolower(str_replace(' ','_',$building))==$apptinfo['building'])
          echo ' selected="true"';
        echo '>'.$building.'</option>';
      }
      ?>
    </select>
    </div>

    <div class="form-group col-xs-6">
    <label for="room">Room</label>
    <input type="text" class="form-control" name="room" value="<?php echo $apptinfo['room']; ?>">
    </div>

    <div class="form-group col-xs-6">
      <label for="tech_id">Technician</label>
      <select class="form-control" name="tech_id">
        <?php
        $users = get_users();
        while ($user = $users->fetch_array()) {
          echo '<option value='.$user['user_id'];
          if ($user['user_id']==$apptinfo['tech_id'])
            echo ' selected="true"';
          echo '>'.$user['fname'].' '.$user['lname'].'</option>';
        }
        ?>
      </select>
    </div>

    <div class="col-xs-6">
    <input type="hidden" name="updateAppt-submit" value="true">
    <button type="submit" class="btn btn-default pull-right">Update</button>
    </div>
  </form>
  <?php
}

function draw_delete_appt_modal() {
  ?>
  <form role="form" action="" method="post">
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
        <p>Really Delete?</p>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="delete_appt-submit" value="true">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Yes, Really Delete</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</form>
  <?
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

<!-- Datepicker today set here -->
<script>

var today = new Date('<?php echo $apptinfo['datetime']; ?>');
var dd = today.getTime();
var mm = today.getMonth()+1; //Jan is 0!
var yyyy = today.getFullYear();

if (dd<10) {
  dd='0'+dd
}

if (mm<10) {
  mm='0'+mm
}

today=yyyy+'-'+mm+'-'+dd;
endyear = yyyy+2;
enddate =endyear+'-'+mm+'-'+dd;

  $('.input-group.date').datepicker({
        format: "yyyy-mm-dd",
        endDate: enddate,
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
</script>