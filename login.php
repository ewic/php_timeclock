<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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

if (isset($_SESSION['validated']) && $_SESSION['validated']) { redirect("index.php"); }	//Logged in users should not be at this page

if (!isset($_POST['username']) AND !isset($_POST['password'])) {
	drawlogin(NULL); 
} //No action yet

elseif (!$_POST['username'] OR  !$_POST['password']) {
		drawlogin("missing"); 
}
elseif (validate($_POST['username'],$_POST['password']))
{
	redirect("index.php");
}

else { 
	drawlogin("invalid"); 
}

ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();

function drawlogin($error) { 
?>
<script type="text/javascript">
 function formfocus() {
 document.getElementById('username').focus();
 }
 window.onload = formfocus;
</script>
<body id="login-body">
<div class="login container center">
			<img src='images/logo.png' /><img src='images/thesystemlogo.png'><br />
			<h3 class="text-muted small">Please Log In</h3>
				<? 	
				if ($error == "invalid") 
					echo "<div class=\"alert alert-danger alert-dismissable\">Invalid Username or Password.<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button></div>";
				if ($error == "missing")
					echo "<div class=\"alert alert-warning alert-dismissable\">Please fill both fields.<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button></div>";
				?>
			<form action='login.php' method='post' role="form">
				<div class="form-group">
					<input type='text' class='form-control' name='username' placeholder='Username' autofocus>
				</div>
				<div class="form-group">
					<input type='password' class='form-control' name='password' placeholder='Password'>
				</div>
				<a href="qmse.php" class="btn btn-default pull-left"><span class="glyphicon glyphicon-calendar"></span> Q.M.S.E.</a>
				<input type='submit' class='btn btn-default pull-right' value='Log In'>
			</form>

</div>
<?
echo "</body>";
}
?>
	


