<?php 

ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();
require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes",$debug);

mysql_init();

//Execute anything
//If trying to update first-pp:
if (isset($_POST['first-pp-submit'])) {
	update_first_payperiod($_POST['first-pp']);
}

//If trying to update pp-length:
if (isset($_POST['pp-length-submit'])) {
	update_payperiod_length($_POST['pp-length']);
}

document_header();
echo include_javascript_dir("js",$debug);
echo include_stylesheet_dir("stylesheets",$debug);

//stupid datepicker
echo '<script src="http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>';

check_validated();
open_page("Maintenance");
draw_page();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function draw_page() 
{	

?>
	<div class="container">

		<?php 
		open_panel('payperiod-info','Payperiod Information', false);
		draw_first_payperiod_form();
		draw_payperiod_length_form();
		close_panel();
		?>
	</div>
	
<?php }

function draw_first_payperiod_form() 
{

	$today = '"'.date('Y-m-d').'"';
	$firstpp = date('Y-m-d',mktime(get_first_payperiod()));
?>
	<form class='form' role="form" action='' method="post">
		<?php echo 'First Payperiod: '.$firstpp; ?>
		<div class="form-group">
			<div class="input-group date">
            	<input type="text" class="form-control col-md-5" name="first-pp"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        	</div>
    	</div>
		
		<input type='hidden' name='first-pp-submit' value='true'>
		<button class="btn btn-default" onclick='submit()'>Update</button>
	</form>

<?
}

function draw_payperiod_length_form() {
	$pplength = get_payperiod_length()/86400;
?>
	<form class='form' role="form" action='' method="post">
		<?php echo 'Payperiod Length (in days): '.$pplength; ?>
		<div class="form-group">
			<div class="input-group">
            	<input type="text" style="border-style:dotted;"class="form-control col-md-2" placeholder="(Struggling with making this smaller...)" name="pp-length"><span class="input-group-addon">Days</span>
        	</div>
    	</div>
		
		<input type='hidden' name='pp-length-submit' value='true'>
		<button type='button' class="btn btn-default" onclick='submit()'>Update</button>
	</form>
	<?
}

?>

<!-- Datepicker today set here -->
<script>

var today = new Date();
var dd = today.getTime();
var mm = today.getMonth()+1; //Jan is 0!
var yyyy = today.getFullYear();

if (dd<10) {
	dd='0'+dd
}

if (mm<10) {
	mm='0'+mm
}

today=yyyy+'-'+mm+'-'+dd;
endyear = yyyy+2;
enddate =endyear+'-'+mm+'-'+dd;

    $('.input-group.date').datepicker({
        format: "yyyy-mm-dd",
        endDate: enddate,
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
</script>