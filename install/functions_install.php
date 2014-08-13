<?php

function install($dbuser,$dbpass) {

	$con = new mysqli($dbhost,$dbuser,$dbpass);

	if($con->connect_errno > 0)
		die('Could not connect: '.$con->connect_error);

	//Creates the database and selects it as default
	$sql = "CREATE DATABASE thesystem";
	$con->query($sql);
	$con->select_db('thesystem');

	//Creates the tables.
	$sql = '';
	$con->query($sql);

	//Creates the configuration file.

}

function create_initial_user() {
	$sql = 'INSERT INTO users VALUES';
}

function create_config() {
	?>
	<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-title"><h3>Config.php</h3></div>
	</div>
		<div class="panel-body">
		<p>Lorem stuff</p>
		</div>
	</div>
	<?php
}

?>