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
if (isset($_POST['editqual-submit']) && isset($_GET['qual_id'])){
  $qualinfo = array();
  $qualinfo = $_POST;
  $qualinfo['qual_id'] = $_GET['qual_id'];
  edit_qual($qualinfo);
}

if (isset($_POST['togglequal-submit']))
  toggle_qual($_GET['qual_id']);

if (count($_SESSION['notifications'])!=0)
  draw_notification();

if (isset($_GET['qual_id']))
  $qualinfo = get_qual_info($_GET['qual_id']);


draw_page($qualinfo);
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

function draw_page($qualinfo) 
{ 

  if ($qualinfo['inactive']==1)
      draw_undelqual($qualinfo);
    else
      draw_delqual($qualinfo);
  
  //Are you page, squire or specialist
  $qualinfo['levelname']=qual_level_to_levelname($qualinfo['level']);

  ?>
  <div class="container">
  <?
  echo "<h3";
  if ($qualinfo['inactive']==1)
    echo ' class="qualinfo inactive"';
  echo '>';
  echo $qualinfo['name'];
  echo '&nbsp;<small>'.$qualinfo['levelname'].'</small>';
  //ACTIVE/INACTIVE STATUS
    //If the user is active, display the deactivate button, else display the activate button.
  echo '<div class="pull-right"  style="cursor:pointer;">';
  if ($qualinfo['inactive']==1)
    echo '<a class="qualinfo inactive" onmouseover="inactiveStatusMouseover(this)" onmouseout="inactiveStatusMouseout(this)" data-toggle="modal" data-target="#togglequal-'.$qualinfo['qual_id'].'">inactive <span class="glyphicon glyphicon-ban-circle"></span></a>';
  else
    echo '<a class="qualinfo active" onmouseover="activeStatusMouseover(this)" onmouseout="activeStatusMouseout(this)" data-toggle="modal" data-target="#togglequal-'.$qualinfo['qual_id'].'">active <span class="glyphicon glyphicon-ok-circle"></span></a>';
  echo '</div>';
  //END ACTIVE/INACTIVE STATUS
  echo '</h3>';

  draw_edit_qual_form($qualinfo);
  ?>
  </div>
  <?php
} 

function draw_edit_qual_form($qualinfo) {
  ?>
  <form action='' method='post' role="form" class="form-horizontal">
        
        <div class="form-group">
          <label for="name" class="col-sm-2 control-label">Name</label>
          <div class="col-sm-10">
          <input class="form-control" type='text' id="name" name='name' value='<?php echo $qualinfo['name'] ?>'>
          </div>
        </div>

        <div class="form-group">
          <label for="url" class="col-sm-2 control-label">url</label>
          <div class="col-sm-10">
          <input class="form-control" type='text' id="url" name='url' value='<?php echo $qualinfo['url'] ?>'>
          </div>
        </div>

        <!-- Qual level - Make sure selected = $qualinfo['level'] -->
        <div class="col-xs-6">
          <div class="form-group">
            <label for="level" class="col-sm-2 control-label">Level</label>
            <select class="form-control" name="level">
              <?php
              for($i=0; $i<3; $i++) {
                echo '<option value='.$i;
                if ($i==$qualinfo['level'])
                  echo ' selected="selected"';
                echo '>';
                echo ucfirst(qual_level_to_levelname($i));
                echo '</option>';
              }
              ?>
            </select>
          </div>
        </div>
        
        <!-- Qual value - Make sure selected = $qualinfo['value'] -->
        <div class="col-xs-6">
          <div class="form-group">
            <label for="value" class="col-sm-2 control-label">Value</label>
            <select class="form-control" name="value">
              <?php
              for($i=1; $i<=10; $i++) {
                $x = $i*10;
                echo '<option value='.$x;
                if ($x==$qualinfo['value'])
                  echo ' selected="selected"';
                echo '>';
                echo $x;
                echo '</option>';
              }
              ?>
            </select>
          </div>
        </div>

      <input type='hidden' name='editqual-submit' value='true'>
      <input type='button' class="btn btn-default" value='Update' onclick='submit()'>
</form>
<?php
}

//Deactivates a user
function draw_delqual($qualinfo) {
  $id = 'togglequal';
  $title = 'Deactivate Qual';

  open_qual_modal($qualinfo,$id,$title);
  ?>
  <div class="modal-body">
    Deactivate qual?
  </div>
  <?php
  close_qual_modal($qualinfo,$id);
}

function draw_undelqual($qualinfo) {
  $id = 'togglequal';
  $title = 'Reactivate Qual';

  open_qual_modal($qualinfo,$id,$title);
  ?>
  <div class="modal-body">
    Reactivate Qual?
  </div> 
  <?php
  close_qual_modal($qualinfo,$id);
}

 ?>

<!-- JAVASCRIPT! -->
<!-- This set of functions changes the appearance of the active/inactive button on mousever/mouseout. -->
 <script type="text/javascript">
 function activeStatusMouseover(element) {
  element.className = "qualinfo inactive";
  element.innerHTML='deactivate <span class="glyphicon glyphicon-ban-circle">';
 }

 function inactiveStatusMouseover(element) {
  element.className = "qualinfo active";
  element.innerHTML='reactivate <span class="glyphicon glyphicon-ok-circle">';
 }

  function activeStatusMouseout(element) {
  element.className = "qualinfo active";
  element.innerHTML='active <span class="glyphicon glyphicon-ok-circle">';
 }

 function inactiveStatusMouseout(element) {
  element.className = "qualinfo inactive";
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

