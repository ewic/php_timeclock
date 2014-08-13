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

//stupid datepicker
echo '<script src="http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>';

//this is the calendar class
include_once("classes/calendar.php");

//Any pre-page logic should go here!
if (isset($_POST['addappt-submit'])) {
	$_POST['datetime'] = $_POST['date'].' '.$_POST['time'].":00:00";
	$_POST['submitter_id']=$_SESSION['user_id'];
	$_POST['tech_id']=$_SESSION['user_id'];
	add_appt($_POST);
}

//Generate the calendar
	$month = isset($_GET['m']) ? $_GET['m'] : NULL;
	$year  = isset($_GET['y']) ? $_GET['y'] : NULL;

	$calendar = Calendar::factory($month, $year);

	$calendar->standard('today')
		->standard('prev-next')
		->standard('holidays');

	$events = array();

	$appts = get_appts();
	while ($appt = $appts->fetch_array()) {
		//convert the datetime to a timestamp, because the calendar does not understand datetimes
		$datetime = new DateTime($appt['datetime']);
		$timestamp = $datetime->getTimestamp();

		//Build the rest of the information
		$title = ucfirst(appt_type_to_string($appt['type']));
		$resolved = '';
		if ($appt['resolved']==TRUE)
			$resolved = ' inactive';

		$output = '<li class="qmse '.appt_type_to_classname($appt['type']).$resolved.'" id="appt-'.$appt['quest_id'].'" onclick="openEdit('.$appt['quest_id'].')">'.$appt['tt'].': '.str_replace('_',' ',ucfirst($appt['building'])).' '.$appt['room'].'</li>';
		$class = appt_type_to_classname($appt['type']);
		$event = $calendar->event()
			->condition('timestamp',$timestamp)
			->title($title)
			->output($output)
			->add_class($class);
		$calendar->attach($event);
	}

is_appointment();

if (isset($_SESSION['user_id']))
  open_page("QMSE");
else
  open_qmse();

draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
	global $calendar
	?>
	<div class="container">
	<?php
	open_panel('calendar','Calendar');
	draw_calendar($calendar);
	close_panel();
	
	open_panel('addappt','Add Appointment');
	draw_add_appt_form();
	close_panel();
?>
	</div>
	<?php
 } 

function draw_calendar($calendar) {
	?>
	<table class="calendar">
				<thead>
					<tr class="navigation">
						<th class="prev-month"><a href="<?php echo htmlspecialchars($calendar->prev_month_url()) ?>"><?php echo $calendar->prev_month() ?></a></th>
						<th colspan="5" class="current-month"><?php echo $calendar->month() ?></th>
						<th class="next-month"><a href="<?php echo htmlspecialchars($calendar->next_month_url()) ?>"><?php echo $calendar->next_month() ?></a></th>
					</tr>
					<tr class="weekdays">
						<?php foreach ($calendar->days() as $day): ?>
							<th><?php echo $day ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($calendar->weeks() as $week): ?>
						<tr>
							<?php foreach ($week as $day): ?>
								<?php
								list($number, $current, $data) = $day;
								
								$classes = array();
								$output  = '';
								
								if (is_array($data))
								{
									$classes = $data['classes'];
									$title   = $data['title'];
									
									if (!empty($data['output'])) {
										if (in_array('today',$data['classes']))
											$classes = array_diff($data['classes'], array('today'));
										else
											$classes = $data['classes'];

										$output .= '<ul class="output">';
										foreach ($data['output'] as $item) {
											$output .= $item;
										}
										$output .= '</ul>';
									}
								}
								?>
								<?php
								if (in_array('today',$data['classes']))
									echo '<td class="day today">';
								else
									echo '<td class="day">';
								?>
								<!-- <td class="day"> -->
									<span class="date"><?php echo $number ?></span>
									<div class="day-content">
										<?php echo $output ?>
									</div>
								</td>
							<?php endforeach ?>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	<?php
}

function draw_add_appt_form() {
	global $buildings;
	$today=date('Y-m-d');
	?>
	<form class="form col-md-4" method="post">
		<h4>Quest</h4>
		<div class="form-group col-xs-6">
			<label for="date">Date</label>
			<div class="input-group date">
            	<input type="text" class="form-control col-md-5" name="date" value=<?php echo '"'.$today.'"'; ?>><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        	</div>
    	</div>
    	<div class="form-group col-xs-6">
    	<label for="time">Time</label>
    			<select class="form-control" name="time">
    				<?php
    				for ($i=10;$i<=14;$i++) {
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
			<label for="tt">Trouble Ticket</label>
			<input type="text" class="form-control" name="tt">
		</div>
		<div class="form-group col-xs-6">
			<label for="tech_id">Technician</label>
			<select class="form-control" name="tech_id">
				<?php
				$users = get_users();
				while ($user = $users->fetch_array()){
					echo '<option value='.$user['user_id'].'>'.$user['fname'].' '.$user['lname'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group col-xs-6">
			<label for="building">Building</label>
			<select class="form-control" name="building">
				<?php
				foreach ($buildings as $building) {
					echo '<option value="'.strtolower(str_replace(' ', '_', $building)).'">'.$building.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group col-xs-6">
			<label for="room">Room</label>
			<input type="text" class="form-control" name="room">
		</div>
		<div class='form-group col-xs-12'>
			<input type="hidden" name="type" value="0">
			<input type="hidden" name="addappt-submit" value="true">
			<button onclick="submit()" class="btn btn-default pull-right">Submit</button>
		</div>
	</form>

	<form class="form col-md-4" method="post">
		<h4>Diagnostic</h4>
		<div class="form-group col-xs-6">
		<label for="date">Date</label>
			<div class="input-group date">
            	<input type="text" class="form-control col-md-5" name="date" value=<?php echo '"'.$today.'"'; ?>><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        	</div>
    	</div>
    	<div class="form-group col-xs-6">
    	<label for="time">Time</label>
    			<select class="form-control" name="time">
    				<?php
    				for ($i=15;$i<=17;$i++) {
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
			<label for="tt">Trouble Ticket</label>
			<input type="text" class="form-control" name="tt">
		</div>
		<div class="form-group col-xs-6">
			<label for="tech_id">Technician</label>
			<select class="form-control" name="tech_id">
				<?php
				$users = get_users();
				while ($user = $users->fetch_array()){
					echo '<option value='.$user['user_id'].'>'.$user['fname'].' '.$user['lname'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group col-xs-6">
			<label for="building">Building</label>
			<select class="form-control" name="building">
				<?php
				foreach ($buildings as $building) {
					echo '<option value="'.strtolower(str_replace(' ', '_', $building)).'">'.$building.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group col-xs-6">
			<label for="room">Room</label>
			<input type="text" class="form-control" name="room">
		</div>
		<div class="form-group col-xs-12">
			<input type="hidden" name="type" value="1">
			<input type="hidden" name="addappt-submit" value="true">
			<button onclick="submit()" class="btn btn-default pull-right">Submit</button>
		</div>
	</form>
	<form class="form col-md-4" method="post">
		<h4>Virus Clinic</h4>
		<div class="form-group col-xs-12">
		<label for="date">Date</label>
			<div class="input-group date">
            	<input type="text" class="form-control col-md-5" name="date" value=<?php echo '"'.$today.'"'; ?>><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        	</div>
    	</div>
    	<div class="form-group col-xs-6">
    		<label for="tt">Trouble Ticket</label>
			<input type="text" class="form-control" name="tt">
		</div>
		<div class="form-group col-xs-6">
			<label for="tech_id">Technician</label>
			<select class="form-control" name="tech_id">
				<?php
				$users = get_users();
				while ($user = $users->fetch_array()){
					echo '<option value='.$user['user_id'].'>'.$user['fname'].' '.$user['lname'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group col-xs-12">
	    	<input type="hidden" name="time" value="18">
	    	<input type="hidden" name="type" value="2">
	    	<input type="hidden" name="building" value="library">
	    	<input type="hidden" name="room" value="helpdesk">
			<input type="hidden" name="addappt-submit" value="true">
			<button onclick="submit()" class="btn btn-default pull-right">Submit</button>
		</div>
	</form>
	<?php
}

//Draws the qmse header, if the user is not logged in.
function open_qmse() {

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

 ?>

 <script type="text/javascript">
 	function openEdit(id) {
  window.open('edit_quest.php?quest_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
 </script>

 <!-- Datepicker today set here -->
<script>

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

<!-- JS for popout -->
<script type="text/javascript">
	function openEdit(id) {
		window.open('edit_appt.php?quest_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
	}
</script>