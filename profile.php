<?php 
ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();

require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes",$debug);

mysql_init();
handle_ajax();
document_header();
echo include_javascript_dir("js");
echo include_stylesheet_dir("stylesheets");

check_validated();
//Any pre-page logic should go here!

//Change password
if (isset($_POST['changepw-submit'])) {
	//Check password
	if ($_POST['newpass'] !== $_POST['checkpass'])
		alert('Passwords do not match!');
	else
		change_password($_POST['newpass']);
}

if(isset($_POST['editform-submit']))
	{  // user has at least tried to submit data
		$userinfo = array();
		$userinfo['user_id'] = $_SESSION['user_id'];
		$userinfo['email'] = $_POST['email'];
		$userinfo['phone'] = $_POST['phone'];
		$userinfo['address'] = $_POST['address'];

		if(validate_data($userinfo))
		{  // data checks out
			update_userinfo($userinfo);
			$userinfo = get_user_info();
		}
		else
		{  // data doesn't check out
			$help=true;
			
		}
	}

$userinfo = get_user_info($_SESSION['user_id']);

open_page("Profile");
draw_page();
close_page();

ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() {
	global $userinfo;
?>
<div class="container">
	<?php open_panel('edituser','User Information', false); ?>
	<div class="form-group col-md-6">
	<?php draw_edit_form($userinfo); ?>
	</div>
	<?php close_panel(); ?>
</div>
<?php 
}

function validate_data($userinfo)
{
	return true;
}



function draw_edit_form($userinfo)
{
?>
	<h3 class="subtle">
			<? echo $userinfo['fname'] . " " . $userinfo['lname']; ?>
			<label for="username">Username:</label>
			<span id="username"><? echo $userinfo['username']; ?></span>
	</h3>
	<button class="btn btn-default btn-sm" data-toggle="modal" data-target="#changepw">Change password</button>
	<form action='' method='post' role="form" class="form">
		
			<label for="email">Email:</label>
			<input class="form-control" type='text' id="email" name="email" value='<?php echo $userinfo['email'] ?>'>

			<label for="phone">Phone:</label>
			<input class="form-control" type='text' id="phone" name='phone' value='<?php echo $userinfo['phone'] ?>'>
	
			<label for="address">Street Address:</label>
			<input class="form-control" type='text' id="address" name='address' value='<?php echo $userinfo['address'] ?>'>

			<input type='hidden' name='editform-submit' value='true'>
			<input type='button' class="btn btn-default pull-right" value='Update' onclick='submit()'>
	</form>
<?

			draw_change_pw();
}

//Print a list of comments made about this person.
function draw_comments_box() {
}

function draw_change_pw() {
	?>
	<div class="modal fade" id='changepw' tabindex='-1' role='dialog' aria-labelledby='adduserLabel' aria-hidden='true'>
    <div class='modal-dialog'>
      <div class='modal-content'>
        <form role='form' method='post' action=''>
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
          <h4 class='modal-title'>Change Password</h4>
        </div>
        <div class="modal-body">
        	<div class="form-group">
        		<label for="newpass">New Password</label>
        	   	<input type="password" class="form-control" name="newpass">
        	</div>
        	<div class="form-group">
        		<label for="checkpass">New Password Again</label>
        		<input type="password" class="form-control" name="checkpass">
        	</div>
        </div>
        <div class="modal-footer">
          <input type='hidden' name='changepw-submit' value='true'>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <input type="submit" class="btn btn-primary" value="OK" name="submit">
        </div>
        </form>
      </div>
    </div>
  </div><!-- close modal -->
  <?
}
?>