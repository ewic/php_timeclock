<?php

ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();

?>

<!-- Bootstrap Stuff -->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<style>
a {
	color: #333;	
}

.install {
	margin-top: 75px;
	max-width: 480px;
	padding: 13px;
	border-style: none;
	border-radius: 5px;
	background-color: #dcdcdc;
	box-shadow: 10px 10px 20px #222222;

	background: -webkit-linear-gradient(-50deg, #f7f7f7, #dcdcdc); /* For Safari 5.1 to 6.0 */
	background: -o-linear-gradient(-50deg, #f7f7f7, #dcdcdc); /* For Opera 11.1 to 12.0 */
	background: -moz-linear-gradient(-50deg, #f7f7f7, #dcdcdc); /* For Firefox 3.6 to 15 */
	background: linear-gradient(-50deg, #f7f7f7, #dcdcdc); /* Standard syntax */

}

.install-body {
	background-color:#001936;
	background-image: url("images/bg.png");
  	background-repeat: repeat-x;
}

.center {
	margin-left:auto;
	margin-right:auto;	
}
</style>

<?php

if (isset($_POST['submit']))
	install($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpass']);

draw_header();
draw_page();
draw_footer();

ob_end_flush(); // Flush the buffer out to client\

function draw_page() { 
?>
<div class=".install-body">
<div class="install container center">
			<h3 class="text-muted small">Root Database Username and Password</h3>
			<form action='' method='post' role="form">
				<div class="form-group">
					<input type='text' class='form-control' name='dbuser' placeholder='Username' autofocus>
				</div>
				<div class="form-group">
					<input type='password' class='form-control' name='dbpass' placeholder='Password'>
				</div>
				<div class="form-group">
					<input type='text' class='form-control' name='dbhost' placeholder='Password'>
				</div>
				<input type='submit' class='btn btn-default pull-right' value='Submit'>
			</form>

</div>
<?
}

function draw_header() {
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html><head>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>the.system</title>
        <meta http-equiv='Content-Type' content='text/html;charset=utf-8' >
        <meta http-equiv="refresh" content="3600">
<body>
<?php
}

function draw_footer() {
	echo '</body>';
  echo '</html>';
}

function 
?>
	


