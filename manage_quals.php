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
if (isset($_POST['qual-input-submit'])){
	echo 'new qual submitted';
	$qualinfo = array();
	$qualinfo['name'] = $_POST['qual_name'];
	$qualinfo['url'] = $_POST['qual_url'];
	$qualinfo['level'] = $_POST['qual_level'];
	$qualinfo['value'] = $_POST['qual_value'];
	add_qual($qualinfo);
}

open_page("Manage Quals");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
?>
	<div class="container">
	<?php open_panel("qual_table","List of Quals", false); ?>
	<?php draw_quals_table(); ?>
	<?php close_panel(); ?>

	<?php open_panel("qual_input","Add a New Qual", false); ?>
	<?php draw_quals_input_form(); ?>
	<?php close_panel(); ?>
	</div>
<?
 }

function draw_quals_table() {
	$quals = get_quals(true);
	?>

	<div class="col-md-10">
		<table class="table table-condensed">
			<tr>
				<th>Name</th>
				<th>Level</th>
				<th>Value</th>
				<th>URL</th>
			</tr>
			<?php
				while($qual = $quals->fetch_array()) {
					//Convert the level to the appropriate name
					$qual['level']=qual_level_to_levelname($qual['level']);
					
					echo '<tr class='.'"quallist';
					if ($qual['inactive']==1)  //If the qual is inactive, this will add "inactive" to its class
						echo ' quallist-inactive';
					echo '" onclick="openEdit('.$qual['qual_id'].')">';
					echo '<td>'.$qual['name'].'</td>';
					echo '<td>'.ucfirst($qual['level']).'</td>';
					echo '<td>'.$qual['value'].'</td>';
					echo '<td><a href="'.$qual['url'].'"> <span class="glyphicon glyphicon-link"></span></a></td>';
					echo '</tr>';
				}
			?>
			<tr>
				<td colspan=2>Total Value</td>
				<td><?php echo get_qual_point_total(); ?></td>
				<td></td>
		</table>
	</div>
	<div class="col-md-2">
		<button class="btn btn-default btn-sm" onclick="toggleinactive()">Toggle inactives</button>
	</div>
	<?php
}

function draw_quals_input_form() {
	?>
	<form role="form" method="post">
		<div class="col-xs-6">
			<div class="col-xs-6">
				<div class="form-group">
					<label for="qual_name">Name</label>
					<input type="text" class="form-control" id="qual_name" name="qual_name" />
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="qual_url">url</label>
					<input type="text" class="form-control" id="qual_url" name="qual_url" />
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
						<label for="qual_level">Level</label>
						<select class="form-control" type="text" id="qual_level" name="qual_level">
						  <option value="0">Page</option>
						  <option value="1">Squire</option>
						  <option value="2">Specialist</option>
					</select>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
						<label for="qual_value">Qual Value</label>
						<select class="form-control" type="text" id="qual_value" name="qual_value">
						  <option value="50">50</option>
						  <option value="75">75</option>
						  <option value="100">100</option>
					</select>
				</div>
			</div>
			<input type="hidden" name="qual-input-submit" value="true">
		<button type="submit" class="btn btn-default pull-right">Submit</button>
		</div>
		<div class="col-xs-6">
			<p>Enter the qual details in the menu to the left
			</p>
		</div>		
	</form>
	<?
}


  ?>

<!-- JS to show inactive users if a button is clicked -->
<script type="text/javascript">
function toggleinactive() {
  var inactives = document.getElementsByClassName("quallist-inactive");
  for(var i=0; i<inactives.length; i++) {
    if( inactives[i].style.display=="table-row")
      inactives[i].style.display="none";
    else 
      inactives[i].style.display="table-row";
  }
}
</script>

<!-- also, don't forget password mismatch js -->

<!-- popup javascript -->
<script type="text/javascript"> 
function openEdit(id) {
  window.open('edit_qual.php?qual_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
</script>
