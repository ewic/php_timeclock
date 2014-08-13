<?php

function handle_ajax() {
	if ($function = get_ajax_function()) {
		if (!isset($_SESSION['validated']) OR !$_SESSION['validated']) {
			echo "NOT AUTHORIZED.  SESSION VALIDATION FAILED.<BR>Reload this page to log back in.";
			exit;
		}
		if ($args = get_ajax_function_args()) { echo call_user_func_array($function,$args); }
		else { echo $function(); }
	mysql_end();
	ob_end_flush();
	exit;
	}
}

function get_ajax_function() {
	if (empty($_POST["fctn"]))
		return false;
	$func_name = $_POST["fctn"];
	return $func_name;
}

function get_ajax_function_args() {
	if (empty($_POST["args"]))
		return false;
	$function_args = $_POST["args"];
	return $function_args;
}
?>