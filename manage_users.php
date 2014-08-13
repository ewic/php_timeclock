<?php 

ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();
require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes",$debug);

mysql_init();

handle_ajax();
document_header();
echo include_javascript_dir("js",$debug);
echo include_stylesheet_dir("stylesheets",$debug);
check_validated();

//If a new user was submitted!
if (isset($_POST['newuser-submit'])) {
  $userinfo = array(
    "username" => $_POST['username'],
    "fname" => $_POST['fname'],
    "lname" => $_POST['lname'],
    "emplid" => $_POST['emplid'],
    "password" => $_POST['password'],
    "email" => $_POST['email'],
    "phone" => $_POST['phone'],
    "address" => $_POST['address'],
    "pwconfirm" => $_POST['pwconfirm'],
    );

  adduser($userinfo);
}

if(!check_app_admin())
  header( 'Location: index.php' );

open_page("Not User Management");	
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

//The actual page.
function draw_page() {	

?>

<div class="container">

  <?php open_panel("userlist","User List",false); ?>

  <div class="row">
    <div class="col-md-10">
      <?php draw_user_list(); ?>
    </div>
    <div class="col-md-2 side_box">
      <button class="btn btn-default btn-sm col-md-12" onClick="toggleinactive()">Show Inactives</button>
      <button class="btn btn-default btn-sm col-md-6" data-toggle="modal" data-target="#adduser">Add User</button>
      <button class="btn btn-default btn-sm col-md-6" data-toggle="modal" data-target="#bulkadd">Bulk Add</button>
    </div>
    <div class="col-md-2 side_box">
      <button class="btn btn-default btn-sm col-md-12" onClick="toggleinactive()">Show Inactives</button>
      <button class="btn btn-default btn-sm col-md-6" data-toggle="modal" data-target="#adduser">Add User</button>
      <button class="btn btn-default btn-sm col-md-6" data-toggle="modal" data-target="#bulkadd">Bulk Add</button>
      
    </div>
  </div>
  <?php close_panel(); ?>


</div>
<?php

draw_adduser_form();
draw_bulkadd_form();
 } 

function draw_user_list() {
  $users = get_users();
  ?>
      <table class="userlist table table-condensed table-bordered">
        <tr>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Username</th>
        </tr>
        <?php
        while ($row = $users->fetch_array()) {
          $row['role'] = get_role($row['user_id']);

          echo '<tr class="userlist ';
          echo 'userlist-'.$row['role'];
          if ($row['inactive'])
            echo ' userlist-inactive';
          echo '" onclick="openEdit('.$row['user_id'].')">';
          echo '<td style="cursor:pointer;">'.$row['lname']."</td>";
          echo '<td style="cursor:pointer;">'.$row['fname']."</td>";
          echo '<td style="cursor:pointer;">'.$row['username']."</td>";
          echo '</tr>';
        } 
        ?>
      </table>
  <?php
}

function draw_adduser_form() { ?>
  <!-- adduser modal -->
  <div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="adduserLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
      <form role="form" method="post" action="manage_users.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="adduserLabel">Add User</h4>
        </div>
        <div class="modal-body">
          
            <div class="form-group">
                <label for="adduser-username">Username</label>
              <input type="text" id="adduser-username" name="username" class= "form-control" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="adduser-fname">First Name</label>
              <input type="text" id="adduser-fname" name="fname" class="form-control" placeholder="Enter first name">
            </div>
            <div class="form-group">
                <label for="adduser-lname">Last Name</label>
              <input type="text" id="adduser-lname" name="lname" class="form-control" placeholder="Enter last name">
            </div>
            <div class="form-group">
                <label for="adduser-emplid">URI ID Number</label>
              <input type="text" id="adduser-emplid" name="emplid" class="form-control" placeholder="Enter URI ID Number">
            </div>
            <div class="form-group">
                <label for="adduser-email">eMail</label>
              <input type="text" id="adduser-email" name="email" class="form-control" placeholder="Enter eMail" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="adduser-phone">Phone</label>
              <input type="text" id="adduser-phone" name="phone" class="form-control" placeholder="Enter Phone" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="adduser-address">Address</label>
              <input type="text" id="adduser-address" name="address" class="form-control" placeholder="Enter Address" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="adduser-password">Password</label>
              <input type="password" id="adduser-password" name="password" class="form-control" autocomplete="off" placeholder="Enter password">
            </div>
            <div class="form-group">
                <label for="adduser-pwconfirm">Confirm Password<span id="pwconfirm-message" style="color:red; display:none"></span></label>
              <input type="password" id="adduser-pwconfirm" name="pwconfirm" class="form-control" placeholder="Enter password again">
            </div>
          
        </div>
        <div class="modal-footer">
          <input type='hidden' name='newuser-submit' value='true'>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <input type="submit" class="btn btn-primary" value="Add User" name="submit">
        </div>
        </form>
      </div>
    </div>
  </div><!-- close modal -->
<?php } 

function draw_bulkadd_form() { ?>
  <!-- adduser modal -->
  <div class="modal fade" id="bulkadd" tabindex="-1" role="dialog" aria-labelledby="bulkaddLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
      <form role="form" method="post" action="manage_users.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="bulkaddLabel">Add Users</h4>
        </div>
        <div class="modal-body">
          
            
          
        </div>
        <div class="modal-footer">
          <input type='hidden' name='bulkadd-submit' value='true'>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <input type="submit" class="btn btn-primary" value="Add Users" name="submit">
        </div>
        </form>
      </div>
    </div>
  </div><!-- close modal -->
<?php } 

function draw_newpass_success($newpass) {
  ?>
  <div class="notice">
    Successfully reset password.  New password is "<?php echo $newpass ?>".
  </div>
  <?php
}

?>
<!-- JS to show inactive users if a button is clicked -->
<script type="text/javascript">
function toggleinactive() {
  var inactives = document.getElementsByClassName("userlist-inactive");
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
  window.open('edit_user.php?user_id='+id,'_blank','height=540,width=680,menubar=no,status=no,titlebar=no,toolbar=no');
}
</script>
