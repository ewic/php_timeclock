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
if (isset($_POST['submit']))
	submit_message($_POST['message']);

open_page("Messaging");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	
?>
	<div class="container">
		<?php open_panel("messagebox","Messaging",false); ?>
			<?php open_panel_item("Messaging","mssg","7")?>
					<?php draw_message_box(); ?>
			<?php close_panel_item()?>
			<?php draw_message_input(); ?>
		<?php close_panel(); ?>
	</div>
<?
 } 

function draw_message_box() {
	$messages = get_messages();
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
	}

}

function draw_message_input() {
	?>
	<form role="form" method="post" action="" style="margin:20px;">
		<div class="form-group">
			<div class="input-group">
				<input type="text" class="text_field form-control" autocomplete="off" placeholder="Enter new message" name="message">
				<span class="input-group-btn"><button class="btn btn-default" type="submit" id="text_submit" name="submit">Submit</button>
				</span>
			</div>
		</div>
	</form>
	<?php
}

 ?>