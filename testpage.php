<?php 

//$debug=true;
ob_start();  // Output buffering - allows header rewrites to happen at anytime before flushing the buffer
session_start();
require_once("incdir.php.inc");
require_once("config.php");

include_php_dir("includes",$debug);

mysql_init();

document_header();
echo include_javascript_dir("js",$debug);
echo include_stylesheet_dir("stylesheets",$debug);

check_validated();
open_page("Main Page"); 
page_logic();
close_page();
ob_end_flush(); // Flush the buffer out to client
document_footer(); mysql_end();


function page_logic() 
{ 
?>

<div class="container">
    <!-- insert dummy data -->

    <?php

    /*
$userinfo = array();

    for ($i=0;$i<20;$i++){
        $userinfo['username'] = 'uname'.$i;
        $userinfo['fname'] = 'fname'.$i;
        $userinfo['lname'] = 'lname'.$i;
        $userinfo['emplid'] = '0000000'.$i;
        $userinfo['barcode'] = '000000000000'.$i;
        $userinfo['email'] = 'email'.$i;
        $userinfo['phone'] = 'phone'.$i;
        $userinfo['address'] = 'address'.$i;
        $userinfo['password'] = 'password'.$i;
        $userinfo['pwconfirm'] = 'password'.$i;
        adduser($userinfo);
        echo "added test user ".$i;
    }
*/

    open_panel('test-target','test_panel');
    open_panel_item('title','color','col');
    ?>
<p>test html</p>
<div>timestamp()</div>
<?php
close_panel();
?>

</div>
<?php
 }


function open_panel_item($title,$color,$col) {


}

?>

