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
/* I know the script for the calendar was giving you trouble, but I'm thinking, eventually, if you can make the date picker smaller, it
would be better than an element stretching the length of the container. Just a thought.*/
echo '<script src="http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>';

check_validated();

if (isset($_POST['goal'])) {
  add_goal($_POST['goal'],$_POST['due_date']);
  }

open_page("Goals"); 
page_logic();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function page_logic() 
{ 
?>
<!-- PAGE CONTAINER -->
<div class="container">
    <?php open_panel("dash2_toggle", "Goals") ?>
      <?php open_panel_item("Goals","goals","6")?>
        <div id="draw_goals_style">
          <?php draw_goals(); ?>
        </div>
        <?php draw_goal_form(); ?>
      <?php close_panel_item()?>
      <?php open_panel_item("Progress","goals","6")?>
        <div id="draw_progress_style">
          <?php draw_progress(); ?>
        </div>
      <?php close_panel_item()?>
    <?php close_panel(); ?>

</div>

    
    
<?php
} 

function draw_goal_form() {

	$today = '"'.date('Y-m-d').'"';

	?>
  <form role="form" method="post" action=""style="margin:20px;">
    <div class="form-group">
      <div class="input-group">
        <input type="text" class="form-control text_field" placeholder="What do you hope to accomplish?" name="message">
        <span class="input-group-btn"><button class="btn btn-default" type="submit" id="text_submit" name="submit">Submit</button>
        </span>
      </div>
    </div>
  </form>
	<?php
}


function draw_progress() {
  $progress = get_progress($_SESSION['user_id']);
  $total = get_qual_point_total();

  open_panel_item('Qual Progress','goals','12');
  draw_qual_progress_bar($progress,$total);
  close_panel_item();
}


function draw_goals() {
	$goals = get_goals();
  ?>
  <table class="table">
    <tr>
      <th>Type</th>
      <th>Goal</th>
      <th>Difficulty</th>
      <th>Due</th>
      <th>Completed?</th>
    </tr>
  <?php
	while ($row = $goals->fetch_array()) {
    echo "<tr>";
    echo "<td>TYPE</td>";
    echo '<td>'.$row["goal"].'</td>';
    echo '<td>DIFFICULTY</td>';
    echo '<td>'.$row["due_date"].'</td>';
    echo '<td>'.$row['completed'].'</td>';
    echo '</tr>';
	}
  ?>
  </table> 
<?php
}

function draw_qual_progress_bar($progress,$total) {
  if ($total==0);
    $total++;
  $percent = round(($progress/$total)*100);
  echo '<div class="progress qual-progress-bar">';
  echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">';
  echo '<span>'.$progress.'/'.$total.'</span>';
  echo '</div>';
  echo '</div>';
}

?>


<script>

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //Jan is 0!
var yyyy = today.getFullYear();

if (dd<10) {
	dd='0'+dd
}

if (mm<10) {
	mm='0'+mm
}

today=yyyy+'-'+mm+'-'+dd;
endyear = yyyy+5;
enddate =endyear+'-'+mm+'-'+dd;

    $('.input-group.date').datepicker({
        format: "yyyy-mm-dd",
        startDate: today,
        endDate: enddate,
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
</script>