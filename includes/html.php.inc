<?
function document_header() {
  global $debug;

  if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
  }

    ?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html><head>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>the.system</title>
        <meta http-equiv='Content-Type' content='text/html;charset=utf-8' >
        <meta http-equiv="refresh" content="3600">

<? 
        }

function document_footer() {
  global $debug, $console;

  //Mystery nbsp to push the floor of the doc down and
  //  accomodate the last panel. 
  echo '&nbsp<br />';
  if ($debug && isset($_SESSION['user_id'])) {
    echo "debugging info<br />";
    echo 'user_id: '.$_SESSION['user_id'];
    echo '<br />You are a(n) '.get_role();
    echo '<br /><b>CONSOLE:</b><br />';
    echo draw_console();
  }
  echo '</html>';
        }

function draw_console() {
  global $console;

  $out = '';
  foreach ($console as $line) {
    $out .= $line.'<br />';
  }

  return $out;

}

function open_page($title) {

            ?>
    <body style='background-image:url("images/grey_bg.jpg");'>
    
        <div>
            <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main_navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">the.system | <?php echo $title ?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="main_navbar">
      
      <ul class="nav navbar-nav navbar-right">

<?php if (check_app_admin()) { ?> 
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">app admin <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_teams.php">Manage Teams</a></li>
            <li><a href="manage_quals.php">Manage Quals</a></li>
            <li><a href="#">TL Evals</a></li>
            <!-- <li><a href="manage_timecards.php">Timecards</a></li> -->
            <li class="divider"></li>
            <li><a href="maintenance.php">Maintenance</a></li>
            <li><a href="../pma">mySQL</a></li>
          </ul>
        </li>

<? } if (check_supervisor()) { ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">supervisor <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <!-- <li><a href="addhours.php">Add hours</a></li> -->
            <li><a href="quals.php">Quals</a></li>
            <li class="divider"></li>
            <li><a href="#">TT Comments</a></li>
            <li><a href="#">Personnel Comments</a></li>
            <li><a href="points.php">Race for Excellence</a></li>
            <li><a href="quals.php"></a></li>
          </ul>
        </li>
<? } ?>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">menu <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="index.php">My Dashboard</a></li>
            <li><a href="qmse.php">QMSE</a></li>
            <li class="divider"></li>
            <li><a href="goals.php">My Goals</a></li>
            <li><a href="messaging.php">My Messages</a></li>
            <li><a href="https://www.mytimestation.com/Login.asp">Punch-in</a></li>
            <li class="divider"></li>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
</nav>
    <?php
    if (count($_SESSION['notifications'])!=0)
      draw_notification();
    ?>
    </div>
<?
}


function close_page() { ?>
    </body>
<? 
} 

function draw_notification() {
  if (isset($_SESSION['notifications'])){
    $_SESSION['notifications']=array_reverse($_SESSION['notifications']);
    ?>
    <div class="container" id="notification">
    <ul>
    <?php 
    while($notification = array_pop($_SESSION['notifications']))
      echo '<li>'.$notification.'</li>';
    ?>
    </ul>
    </div>

    <script>
      $( document ).ready(function() {
        $( "#notification" ).slideDown( "slow" );
      });
    </script>
    <?
  }
}

function alert($alert) {
  ?>
  <div class="alert alert-danger fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><?php echo $alert; ?></h4>
  </div>
  <?php
}

//Draws the title bar and styling for a panel
function open_panel($target,$title, $collapsible=true) {
  ?>
  <div class="row row-toggle" id="main_segment_toggle" 
  <?php 
  if ($collapsible)
    echo 'data-toggle="collapse" data-target="#'.$target.'"';
  ?>>
    <p><?php echo $title; ?></p>
    </div>
  <?php
    echo '<div ';
    if ($collapsible)
      echo 'class="main_panel row collapse in"';
    else 
      echo 'class="main_panel panel row"';
    echo 'id="'.$target.'">';
}

//closes a panel
function close_panel() {
  echo "</div>";
}

function open_panel_item($title, $class, $width) {
  ?>
  <div class="col-md-<?php echo $width ?>">
  <div class="panel-item  <?php echo $class ?>">
  <div class="panel-item-title"><h2><?php echo $title; ?></h2></div>
  <div class="panel-item-content spacer-div">

  <?php
}

function close_panel_item() {
  echo '</div></div></div>';
}
?>
