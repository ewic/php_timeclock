<?php 

ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();
require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes",$debug);

mysql_init();

document_header();

echo include_javascript_dir("js",$debug);
echo include_stylesheet_dir("stylesheets",$debug);

if (isset($_POST['barcode']) && $_POST['barcode']!="") {
  $punch = punch($_POST['barcode']);
}

if (isset($_POST['barcode']) && $_POST['barcode']=="") {
  $punch = punch(id_to_barcode($_SESSION['user_id']));
}

if (isset($_SESSION['user_id']))
  open_page("Punchclock");
else
  open_punchclock();

page_logic();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();
?>
<script type="text/javascript">
window.onload = function startTime()
 {
 var today=new Date();
 var h=today.getHours();
 var m=today.getMinutes();
 var s=today.getSeconds();
 var ampm = "am";

 //Change to 12-hour clodk
 if (h >12) {
 	h -= 12;
 	ampm = "pm";
 }
 else if (h === 0) {
 	h = 12;
 }

 // add a zero in front of numbers<10
 h=checkTime(h);
 m=checkTime(m);
 s=checkTime(s);
 document.getElementById('clock').innerHTML=h+":"+m+":"+s+" "+ampm;
 t=setTimeout(function(){startTime()},500);
 }

 function checkTime(i)
 {
 if (i<10)
   {
   i="0" + i;
   }
 return i;
 }

//Focus on the input field
var input = document.getElementById("barcode").focus();

 </script>
<?

function page_logic() {
?>
<div class="container">
  <?php open_panel("dash2_toggle","Clock"); ?>
    <div class="clock">
      <div id="clock">
      </div>
    </div>
  <?php close_panel(); ?>
<!-- If the user is logged in, display their timecard -->
<?php
if (isset($_SESSION['user_id'])) {
  ?>
  <?php open_panel("dash3_toggle","Time Card"); ?>
    <?php draw_timecard(); ?>
  <?php close_panel(); ?>  
<?php
}
?>
</div>
<?
 }

//Draws the punchclock header, if the user is not logged in.
function open_punchclock() {

            ?>
    <body>
        <div id='page'>
            <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main_navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="punchclock.php">the.system | Punchclock</a>
    </div> 

    <div class="collapse navbar-collapse" id="main_navbar">
      
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php"><span class="glyphicon glyphicon-home"></span> Log-In</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
</nav>
    </div>
<?
}

//Draws a timecard
function draw_timecard($payperiod=NULL, $user_id=NULL) {
  if ($payperiod)
    $payperiod=current_payperiod();

  if (!$user_id)
    $user_id=$_SESSION['user_id'];

  $user = get_user_info($user_id);

?><!--<div class="row row-toggle" id="main_segment_toggle" data-toggle="collapse" data-target="#dash3_toggle"><p>Your Time Card</p>
    </div>
    <div class="row collapse in" id="dash3_toggle" id="punch_clock_panel">-->
  <div class="col-md-8">
    <table class="table" id="punch_card_table">
      <tr><td colspan=4><center><h4>Your Time Card</h4></center></td></tr>
      <tr>
        <th class="punch_day" width="100">Date</th>
        <th class="punch_time">Time</th>
        <th class="punch_status">Punch Status</th>
        <th class="punch_hours">Hours</th>
      </tr>
<?php
    $punches = get_punch_data($user_id);
    $inout = '';
    $additional_hours = get_additional_hours($user_id,$payperiod);
    $total = 0;
    while ($row = $punches->fetch_array()) {
      $day = date('n/j',strtotime($row['timestamp']));
      $time = date('h:i a',strtotime($row['timestamp']));
      if ($inout=='in'){
        $inout = '<font color="#999">out</font>';
        $hours = count_hours($intime,mktime(strtotime($row['timestamp'])));
        //If hours is more than 8, then you probably forgot to punch out...
        if ($hours>8)
          alert("Did you forget to punch out?");        
        $total += $hours;
      }
      else {
        $inout = 'in';
        $intime = mktime(strtotime($row['timestamp']));
        $hours = '';
      }
      echo '<tr>';
      echo '<td class="punch_day">'.$day.'</td>';
      echo '<td class="punch_time">'.$time.'</td>';
      echo '<td class="punch_status">'.strtoupper($inout).'</td>';
      echo '<td class="punch_hours">'.$hours.'</td>';
      echo '</tr>';
    }
?>
      
    </table>

  </div>
<div class="col-md-4">

  <form role="form" action="punchclock.php" method="post">
    <div class="input-group" style="float:right;">
     <input type="password" class="form-control text_field" placeholder="ID Barcode" id="barcode" name="barcode">
     <span class="input-group-btn">
       <button class="btn btn-default" id="text_submit" type="submit"><span class="glyphicon glyphicon-time"></span>

<?php
        if (is_punched_in($_SESSION['user_id']))
          echo '<b>Punch Out</b>';
        else
          echo '<b>Punch In</b>';
        ?>

       </button>
     </span>
    </div>
  </form> 
  <P><BR><hr>
      <table class="table" id="punch_info">
        <tr><td colspan=2><center><h4>This Pay Period</h4></center></td></tr>
      <tr id="punch_coloredCells">
        <td><b>Start Date:</b> <?php echo date('m/d/Y',get_payperiod_start()); ?></td>
        <td><b>End Date:</b> <?php echo date('m/d/Y',get_payperiod_end()); ?></td>
      </tr>
      <tr>
        <td><?php echo ucfirst($user['lname']).', '.ucfirst($user['fname']); ?></td>
        <td><?php echo $user['emplid']; ?></td>
      </tr>
      <tr id="punch_coloredCells">
        <td><b>Subtotal: </b></td>
        <td><?php echo $total; ?></td>
      </tr> 
      <tr>
        <td>Additional Hours: </td>
        <td><?php echo $additional_hours; ?></td>
      </tr> 
      
      <tr>
        <td>Total Hours</td>
        <td><?php echo $total+$additional_hours; ?></td>
      </tr>  
    </table>
  </div>
<?php
//We can't work more than 20 hours.
if ($total+$additional_hours>20)
  alert("You are over!  Please double check your punches.");
}

?>