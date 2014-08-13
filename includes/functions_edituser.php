<?php

//Make a modal!
function open_user_modal($userinfo,$id,$title) {
  ?>
  <div class="modal fade" id='<?php echo $id.'-'.$userinfo['user_id']; ?>' tabindex='-1' role='dialog' aria-labelledby='adduserLabel' aria-hidden='true'>
    <div class='modal-dialog'>
      <div class='modal-content'>
        <form role='form' method='post' action=''>
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
          <h4 class='modal-title'><?php echo $title; ?></h4>
        </div>
<?php }

function close_user_modal($userinfo,$id) {
  ?>
        <div class="modal-footer">
          <input type='hidden' name='<?php echo $id; ?>-submit' value='true'>
          <input type='hidden' name='<?php echo $id; ?>-id' value='<?php echo $userinfo['user_id']; ?>'>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <input type="submit" class="btn btn-primary" value="OK" name="submit">
        </div>
        </form>
      </div>
    </div>
  </div><!-- close modal -->
  <?php
 
}

?>