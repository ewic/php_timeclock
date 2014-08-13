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

if (isset($_POST['teamchange-submit']))
	add_to_team($_POST['user_id'],$_POST['tl_id']);

if (isset($_POST['teamremove-submit']))
	remove_from_team($_POST['user_id']);

open_page("Manage Teams");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
?>
	<div class="container">

		<?php open_panel("teamlist", "Teams", false) ?>
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
				while ($tl = $team_leaders->fetch_array()) {
					$team_members = get_team_members($tl['user_id']);
					?>
					<div class="col-md-3">
					<table class="table table-condensed" id="teams_table">
					<tr>
						<th><?php echo $tl['lname'].', '.$tl['fname']; ?></th>
						<th></th>
					</tr>
					<?php
					while ($tm = $team_members->fetch_array()) {
						?>
						<tr>
							<td>&nbsp;&nbsp;<?php echo $tm['lname'].', '.$tm['fname']; ?></td>
							<td><?php draw_remove_from_team_form($tm['user_id']); ?></td>
						</tr>
						<?
					}
					?>
					</table>
					</div>
					<?php
				}
				?>
		<div class="col-md-12">
		<?php
		$orphans = get_orphans();
		echo '<table class="table table-condensed" id="orphans_table">';
		while ($orphan = $orphans->fetch_array()) {
					echo '<tr><td>'.$orphan['lname'].','.$orphan['fname'].'</td></tr>';
				}
		echo '</table>';
		?>
		</div>

		</div>
		<?php close_panel(); ?>
		<!-- END LIST OF TEAMS AND MEMBERS -->

		<?php open_panel("add-to-team-form", "Add to Team", false) ?>

		<?php draw_add_to_team_form(); ?>
		
		<div class="col-md-6">
		
	</div>
	<?php close_panel() ?>
	</div>
	<?php
}

function draw_add_to_team_form() {
	$orphans = get_orphans();
	$team_leaders = get_team_leaders();
	?>
	<form role="form" class="form-inline" method="post">
	add:
	<select class="form-control" name="user_id"> 
		<?php
		while($orphan = $orphans->fetch_array())
			echo '<option value='.$orphan['user_id'].'>'.$orphan['lname'].', '.$orphan['fname'].'</option>';
		?>
	</select>
	to team: 
	<select class="form-control" name="tl_id">
		<?php
		while ($tl = $team_leaders->fetch_array())
			echo '<option value='.$tl['user_id'].'>'.$tl['lname'].', '.$tl['fname'].'</option>';
		?>
	</select>
	<input type="hidden" name="teamchange-submit">
	<input type="submit" class="btn btn-default">
	</form>
	<?php
}

//Draw the button to remove a user from a team
function draw_remove_from_team_form($user_id) {
	?>
	<form role="form" method="post">
	<input type="hidden" name="teamremove-submit" value="true">
	<input type="hidden" name="user_id" value=<?php echo $user_id ?>>
	<input type="submit" class="close" name="submit" value="X">
	</form>
<?
 }

//Draw the form to add a user to a team
function draw_team_form() {
	$team_leaders = get_team_leaders();
	$orphans = get_orphans();
	?>
	
	<?php
}

?>

