<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();
require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes",$debug);

mysql_init();

document_header();
echo include_javascript_dir("js",$debug);
echo include_stylesheet_dir("stylesheets",$debug);

//stupid datepicker
echo '<script src="http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>';

check_validated();

if (isset($_POST['addpoints-submit']))
  add_points(strtolower($_POST['note']),$_POST['value'],$_POST['user_id']);
if (isset($_POST['addabsence-submit']))
	add_ponts('absence',-5,$_POST['user_id']);

open_page("Dashboard");	
draw_index();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

function draw_index() 
{	
?>
<div class="container">
<!-- Row 1 -->
	<?php open_panel("dash2_toggle", "QMSE & TimeStation"); ?>
		<!--CALENDAR PANEL-ITEM -->
		<?php open_panel_item("Quests and Appointments", "hours", "7"); ?>
		

		<!-- If nothing scheduled 
		<P>No Quests or Appointments.
		<P><a href="qmse.php"><span style="font-size:.8em; font-style:italic;">View Calendar</span></a>-->
		<?php draw_qmse_panel(); ?>
		<?php close_panel_item(); ?>
		<!--HOURS PANEL-ITEM 
		<?php open_panel_item(/*"Hours", "hours", "6"*/); ?>
		Content of "Hours"
		<?php close_panel_item() ?>-->
		<!--PUNCH PANEL-ITEM -->
		<?php open_panel_item("<center><a href='https://www.mytimestation.com/Login.asp'><img src='images/ts_logo_trans.gif' /></a></center>", "punch", "5"); ?><a href="https://www.mytimestation.com/Login.asp"><img src="images/ts_screen.png" style="width:100%; height:100%;"/></a></center>
		<?php close_panel_item(); ?>
<!--CLOSE PANEL -->
	<?php close_panel(); ?>
	<?php open_panel("dash3_toggle","Goals | Quals | Messages"); ?>
		<!-- GOALS PANEL ITEM -->
		<?php open_panel_item("Goals", "goals", "6"); ?>
			<div>
				<?php draw_goals_dash(); ?>
			</div>
		<?php close_panel_item(); ?>
		<!-- QUALS PANEL ITEM -->
		<?php open_panel_item("Quals", "goals", "6"); ?>
		<?php draw_quals_panel_item(); ?>
		<?php close_panel_item(); ?>
		<!-- MESSAGING PANEL ITEM -->
		<?php open_panel_item("Messaging", "mssg", "6"); ?>
		<?php draw_messages_panel() ?>
		<?php close_panel_item(); ?>
		<!-- CLOSE PANEL -->
	<?php close_panel(); ?>
<!-- Row 3-->
<!-- for app supers or app admins only -->
	<?php if (check_supervisor())
	{ 
		open_panel("teamlist", "Team Leader Panel") ?>
		<?php open_panel_item("<a href='manage_teams.php'>Team Makeup</a>","tl_item","12")?>
		<?php draw_teamlist() ?>
		<?php close_panel_item();?>

			<?php //open_panel_item("Additional Hours", "tl_item", "4"); ?>
			<?php //draw_additional_hours() ?>
			<?php //close_panel_item(); ?>
			<?php open_panel_item("Race!", "tl_item", "6"); ?>
			<?php draw_race_form() ?>
			<?php close_panel_item(); ?>
			<?php open_panel_item("Absence", "tl_item", "6"); ?>
			<?php draw_absence_form() ?>
			<?php close_panel_item(); ?>
		<?php close_panel(); 

		
	} ?>

</div>
<?
 }

function draw_teamlist() {
	?>
	<div class="row">
			<!-- LIST OF TEAMS AND MEMBERS -->
			<?php
			/* 
			Walkthrough of the code:
			-Ask mysql for a list of all team leaders (anybody who is an app_supervisor).
			-Step through each tl
			-Draw a new table for each tl
			-Ask mysql for a list of all team members of that particular team leader
			-Step through each team member and draw a <td> for each one
			*/
			$team_leaders = get_team_leaders();
			?>
			
				<?php
				while ($tl = $team_leaders->fetch_array()) 
				{
					$team_members = get_team_members($tl['user_id']);
					?><div class="col-md-3">
					<table class="table table-condensed" id="teams_table">
						<tr>
							<th><?php echo $tl['lname'].', '.$tl['fname']; ?></th>
							
						</tr>
						<?php
						while ($tm = $team_members->fetch_array()) {
							?>
							<tr>
								<td>&nbsp;&nbsp;<?php echo $tm['lname'].', '.$tm['fname']; ?></td>
								
							</tr>
							<?
						}
						?>
					</table>
					</div>
					<?php
				}
				?>
			
		</div>
	<?php
}

function draw_goals_dash()
{
	?>
	<div class="draw_inset_div goals_inset">
		<?php
			$goals = get_goals();
			while ($row = $goals->fetch_array())
				{
					?>
						<input type="checkbox" style="margin:10px 4px 0px 0px;">
						<?php
			    			echo $row["goal"].' - ';
			    			echo $row["due_date"]."<br>";
				}
		?>
	</div>
	<?php
	/*return 'goals';*/
}

/* DEPRECATED: REMOVED TIMECARD FUNCTIONALITY
function draw_additional_hours() {
	$users=get_users(false);
	?>
	<!-- Add hours form -->
	<form role="form" action="index.php" method="post">
	<div class="input-group">
		<select class="form-control" name="user_id">
			<?php
			while ($row = $users->fetch_array()) {
				echo "<option value=".$row['user_id'].">";
				echo $row['lname'].', '.$row['fname'];
				echo "</option>";
			}
			?>
		</select>
	</div>
	<div class="input-group">
		<label for="hours">Hours</label>
		<input type="text" class="form-control" name="hours">
		<div class="input-group-btn">

			<button type="button" id="quarterhour-label" class="btn btn-default dropdown-toggle" data-toggle="dropdown">.00<span class="caret"></span></button>
        <ul class="dropdown-menu pull-right">
          <li><a onclick="addquarterhour(0)">.00</a></li>
          <li><a onclick="addquarterhour(1)">.25</a></li>
          <li><a onclick="addquarterhour(2)">.50</a></li>
          <li><a onclick="addquarterhour(3)">.75</a></li>
        </ul>

		</div>
	</div>
	<input type='hidden' name='quarter' id='quarterhour-input' value='0'>
	<input type='hidden' name='addhours-submit' value='true'>
	<input class="btn btn-default btn-sm" type="submit">
	</form>
	<?php
}
*/


function draw_qmse_panel() {
	$appts = get_appts('week');
	if ($appts->num_rows == 0)
		echo '<h4>There are no appointments this week</h4>';
	else {
		?>
		<table class="table table-condensed">
			<tr>
				<th>Date</th>
				<th>Time</th>
				<th>Trouble Ticket</th>
				<th>Location</th>
			</tr>
			<?php
			while ($appt = $appts->fetch_array()) {
				$appt['timestamp'] = strtotime($appt['datetime']);
				echo '<tr>';
				echo '<td>'.date("D, m/d",$appt['timestamp']).'</td>';
				echo '<td>'.date("h:i a",$appt['timestamp']).'</td>';
				echo '<td>'.$appt['tt'].'</td>';
				echo '<td>'.str_replace('_',' ',ucfirst($appt['building'])).' '.$appt['room'].'</td>';
				echo '</tr>';
			}
			?>
		</table>
	<?php
	}
}

function draw_race_form() {
	global $points_menu;

	$users = get_users('user');
	?>
	<form role="form" action='' method="post">
	<div class="row">
		<div class="col-xs-12">
			<div class="input-group">
			<label for="user_id">Consultant</label>
				<select class="form-control" name="user_id">
					<?php
					while ($row = $users->fetch_array()) {
						echo "<option value=".$row['user_id'].">";
						echo $row['lname'].', '.$row['fname'];
						echo "</option>";
					}
					?>
				</select>
			</div>
		</div>
	</div>
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
	  <div class="col-xs-3">
	    <input type="hidden" name="addpoints-submit" value="true">
	    <button class="btn btn-default btn-sm" onclick="submit()">Submit</button>
	  </div>
	</div>
	
	</form>
	<?php
}

function draw_quals_panel_item() {
	$progress = get_progress();
	$total = get_qual_point_total();
	draw_qual_progress_bar($progress,$total);
}

//For the quals panel item
function draw_qual_progress_bar($progress,$total) {
	//Avoid division by zero issues
	if ($total==0)
		$total++;
  $percent = round(($progress/$total)*100);
  echo '<div class="progress qual-progress-bar">';
  echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">';
  echo '<span>'.$progress.'/'.$total.'</span>';
  echo '</div>';
  echo '</div>';
}

function draw_absence_form() {
	$users = get_users('user');
	?>
	<form role="form" action='' method="post">
	<div class="row">
		<div class="col-xs-12">
			<div class="input-group">
			<label for="user_id">Consultant</label>
				<select class="form-control" name="user_id">
					<?php
					while ($row = $users->fetch_array()) {
						echo "<option value=".$row['user_id'].">";
						echo $row['lname'].', '.$row['fname'];
						echo "</option>";
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
	  <div class="col-xs-3">
	    <input type="hidden" name="addpoints-submit" value="true">
	    <button class="btn btn-default btn-sm" onclick="submit()">Submit</button>
	  </div>
	</div>
	</form>
	<?php
}

function draw_messages_panel() {
	?>
	<div class="draw_inset_div">
	<?php $messages = get_messages();
	while ($message = $messages->fetch_array()){
		?><div class="mssg_text"><b>
		<?php
		echo '<div class=';
		if ($message['user_id']==$_SESSION['user_id'])
			echo 'mssg-self';
		else if ($message['user_id']==get_team_leader())
			echo 'mssg-tl';
		echo '>';
		echo ucfirst($message['fname']).' ';
		echo ucfirst($message['lname']);
		echo '</div>';
		?></b>
		<?php
		echo '<BR>'.$message['message'];
		echo '<BR></div><P>';
	}?>
		</div>
	<?php
}

?>

<!-- Datepicker today set here -->
<script type="text/javascript">
var today = new Date();
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

<script type="text/javascript">
function set_active() {
	for(var i=0;i<5;i++){
		print i;
	}

}

</script>
<script type="text/javascript">
function clicked_tab(id, itemName) {
	var current = document.getElementById("tlcontent");
	var clicked = document.getElementById(id);
	current.innerHTML = clicked.innerHTML;
	document.getElementById("item0").className="dash-list-default";
	for(var i=0;i<6;i++){
	document.getElementById("item"+i).className="dash-list-default";
}
	document.getElementById(itemName).className="dash-list-selected";
}
</script>

<!-- JS function to handle quarter hours in the add additional hours form -->
<!-- Deprecated, removed timecard functionality
<script>
function addquarterhour(i) {
	var label = document.getElementById('quarterhour-label');
	var input = document.getElementById('quarterhour-input');
}
</script>
-->
