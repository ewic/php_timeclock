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

//If a user was deleted...
if (isset($_POST['deluser-submit']))
  delete_user($_POST['deluser-id']);

//If a user was undeleted...
if (isset($_POST['undeluser-submit']))
  undelete_user($_POST['undeluser-id']);

//If a user was made admin
if (isset($_POST['makeadmin-submit']))
  make_admin($_POST['makeadmin-id']);

//If a user was unmade admin
if (isset($_POST['unmakeadmin-submit']))
  unmake_admin($_POST['unmakeadmin-id']);

//If a user was made super
if (isset($_POST['makesuper-submit']))
  make_super($_POST['makesuper-id']);

//If a user was unmade super
if (isset($_POST['unmakesuper-submit']))
  unmake_super($_POST['unmakesuper-id']);

//If a user had his password reset
if (isset($_POST['pwreset-submit'])) {
  $newpass = reset_password($_POST['pwreset-id']);
  draw_newpass_success($newpass);
}

if (isset($_POST['edituser-submit']))
  edit_user($_POST);

if (isset($_GET['user_id']))
  $userinfo = get_user_info($_GET['user_id']);

if (count($_SESSION['notifications'])!=0)
  draw_notification();

draw_page($userinfo);
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

function draw_page($userinfo) 
{ 
  $userinfo['role'] = get_role($userinfo['user_id']);

          draw_pwreset($userinfo);

          if ($userinfo['inactive']==1)
            draw_undeluser($userinfo);
          else
            draw_deluser($userinfo);

          if ($userinfo['role']!='admin')
            draw_makeadmin($userinfo);
          else
            draw_unmakeadmin($userinfo);

          if ($userinfo['role']!='super')
            draw_makesuper($userinfo);
          else
            draw_unmakesuper($userinfo);
?>
    <div class="container">
<?php 
  echo '<h3';
  if ($userinfo['inactive']==1)
    echo ' class="userinfo inactive"';
  echo '>';
  echo $userinfo['fname'].' '.$userinfo['lname'];
  echo '&nbsp;<small>'.$userinfo['role'].'</small>';

  //ACTIVE/INACTIVE STATUS
    //If the user is active, display the deactivate button, else display the activate button.
  echo '<div class="pull-right" style="cursor:pointer;">';
  if ($userinfo['inactive']==1)
    echo '<a class="userinfo inactive" onmouseover="inactiveStatusMouseover(this)" onmouseout="inactiveStatusMouseout(this)" data-toggle="modal" data-target="#undeluser-'.$userinfo['user_id'].'">inactive <span class="glyphicon glyphicon-ban-circle"></span></a>';
  else
    echo '<a class="userinfo active" onmouseover="activeStatusMouseover(this)" onmouseout="activeStatusMouseout(this)" data-toggle="modal" data-target="#deluser-'.$userinfo['user_id'].'">active <span class="glyphicon glyphicon-ok-circle"></span></a>';
  echo '</div>';
  //END ACTIVE/INACTIVE STATUS
  
  echo '</h3>';


  //BUTTON GROUP
  echo '<div class="btn-group btn-group-sm">';
  echo '<button class="btn btn-default" data-toggle="modal" data-target="#pwreset-'.$userinfo['user_id'].'"">reset password</button>';

    //If the user is not an admin, display the makeadmin button, else display the unmakeadmin button.
  if ($userinfo['role']!='admin')
    echo '<button class="btn btn-default" data-toggle="modal" data-target="#makeadmin-'.$userinfo['user_id'].'">Make Admin</button>';
  else 
    echo '<button class="btn btn-default" data-toggle="modal" data-target="#unmakeadmin-'.$userinfo['user_id'].'">Remove from Admins</button>';

  if (check_app_admin($userinfo['user_id'])) 
    echo '<div class=" btn-defaultbtn">user is an admin</div>';

  //If the user is neither an admin nor a supervisor, then draw a makesuper button
  else if ($userinfo['role']!='super')
    echo '<button class="btn btn-default" data-toggle="modal" data-target="#makesuper-'.$userinfo['user_id'].'">Make Team Leader</button>';

  //If the user is a supervisor, but not an admin, draw an unmakesuper button
  else 
    echo '<button class="btn btn-default" data-toggle="modal" data-target="#unmakesuper-'.$userinfo['user_id'].'">Remove from Team Leaders</button>';
  echo '</div>'; //END BUTTON GROUP

  draw_edit_user_form($userinfo);
?>

  </div>
  <?php
 } 

function draw_edit_user_form($userinfo){
  ?>
  <form action='' method='post' role="form" class="form-horizontal">
        
        <div class="form-group">
          <label for="username" class="col-sm-2 control-label">Username:</label>
          <div class="col-sm-10">
          <input class="form-control" type='text' id="username" name='username' value='<?php echo $userinfo['username'] ?>'>
          </div>
        </div>

        <div class="form-group">
          <label for="fname" class="col-sm-2 control-label">First Name:</label>
          <div class="col-sm-10">
          <input class="form-control" type='text' id="fname" name='fname' value='<?php echo $userinfo['fname'] ?>'>
          </div>
        </div>

        <div class="form-group">
          <label for="lname" class="col-sm-2 control-label">Last Name:</label>
          <div class="col-sm-10">
          <input class="form-control" type='text' id="lname" name='lname' value='<?php echo $userinfo['lname'] ?>'>
          </div>
        </div>
        
        <div class="form-group">
          <label for="emplid" class="col-sm-2 control-label">emplid:</label>
          <div class="col-sm-10">
          <input class="form-control" type='text' id="emplid" name="emplid" value='<?php echo $userinfo['emplid'] ?>'>
          </div>
        </div>

      <input type='hidden' name='user_id' value='<?php echo $_GET['user_id']; ?>'>
      <input type='hidden' name='edituser-submit' value='true'>
      <input type='button' class="btn btn-default" value='Update' onclick='submit()'>
</form>
<?
}

function draw_newpass_success($newpass) {
  alert("User's password has been changed to \"".$newpass."\".  Make sure to let them know!");
}

//Deactivates a user
function draw_deluser($userinfo) {
  $id = 'deluser';
  $title = 'Deactivate User';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
    Deactivate user <? echo $userinfo['fname'].' '.$userinfo['lname']; ?>?
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

//Reactivates a user
function draw_undeluser($userinfo) {
  $id = 'undeluser';
  $title = 'Reactivate User';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
    Reactivate user <? echo $userinfo['fname'].' '.$userinfo['lname']; ?>?
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

//Make a user an admin
function draw_makeadmin($userinfo) {
  $id = 'makeadmin';
  $title = 'Add to Admins';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
    Adding user <? echo $userinfo['fname'].' '.$userinfo['lname']; ?> to admins!<br />
    If this user is a team leader, his/her entire team will be dropped.
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

//Unmake a user an admin
function draw_unmakeadmin($userinfo) {
  $id = 'unmakeadmin';
  $title = 'Remove from Admins';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
    Remove user <? echo $userinfo['fname'].' '.$userinfo['lname']; ?> from admins?
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

//make a super
function draw_makesuper($userinfo) {
  $id = 'makesuper';
  $title = 'Add to Supervisors';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
    Adding user <?php echo $userinfo['fname'].' '.$userinfo['lname']; ?> to supervisors!
    If this user is part of a team, he will be removed.
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

//unmake a super
function draw_unmakesuper($userinfo) {
  $id = 'unmakesuper';
  $title = 'Remove from Supervisors';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
    Removing user <? echo $userinfo['fname'].' '.$userinfo['lname']; ?> from supervisors!<br />
    If this user has a team, that team will dissolve!
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

//pwreset form
function draw_pwreset($userinfo) {
  $id = 'pwreset';
  $title = 'Reset Password';

  open_user_modal($userinfo,$id,$title);
  ?>
  <div class="modal-body">
  Reset password for <? echo $userinfo['fname'].' '.$userinfo['lname']; ?>?
  </div>
  <?php
  close_user_modal($userinfo,$id);
}

 ?>

<!-- JAVASCRIPT! -->
<!-- This set of functions changes the appearance of the active/inactive button on mousever/mouseout. -->
 <script type="text/javascript">
 function activeStatusMouseover(element) {
  element.className = "userinfo inactive";
  element.innerHTML='deactivate <span class="glyphicon glyphicon-ban-circle">';
 }

 function inactiveStatusMouseover(element) {
  element.className = "userinfo active";
  element.innerHTML='reactivate <span class="glyphicon glyphicon-ok-circle">';
 }

  function activeStatusMouseout(element) {
  element.className = "userinfo active";
  element.innerHTML='active <span class="glyphicon glyphicon-ok-circle">';
 }

 function inactiveStatusMouseout(element) {
  element.className = "userinfo inactive";
  element.innerHTML='inactive <span class="glyphicon glyphicon-ban-circle">';
 }
</script>

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

