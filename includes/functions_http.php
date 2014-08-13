<?php

// Just a function that returns whether or not a session is a vailidated (authenticated session)
// If not, they will be redirected to the login page.

function check_validated()
{
	if (!isset($_SESSION['validated']) OR !$_SESSION['validated'])
	{
		redirect("login.php");
	}
	return true;
}

/* Function to write a redirect header.  This requires that output buffering be turned ON.
 $relurl is the relative path to redirect to.  This will not redirect to pages that are not
 on the document root. */

function redirect($relurl)
{
	if (!headers_sent()) {
		header("Location: ".$DOC_ROOT.$relurl);
	}
	else {
		echo "headers already sent<br>";
	}
	exit;
}

/* This will set the page to refresh every $sec seconds. 
It must be included in the header of the page */

function set_page_refresh($sec) {
	echo "<meta http-equiv='refresh' content='".$sec."'>";	
}

/* This is used for pages displayed in Javascripted new windows.  It will reload the contents
  of the page that opened this one. Useful for making changes in a new window and having them
  automatically appear in the main window.*/

function refresh_parent() {
	echo "<script language='JavaScript' type='text/javascript'>
  		<!--
    		opener.location.reload(true);
  		// -->
	</script>\n";
}

/* Same as the above function, but makes the parent window redirect to a different web address */

function parent_goto($url) {
	echo "<script language='JavaScript' type='text/javascript'>
  		<!--
    		opener.location.href=\"".$url."\";
  		// -->
	</script>\n";
}

/* Closes an active window.  This is used for automatically closing windows after an action has been completed.
  Used in this application to separate backend work from the web pages.  Switching a position will open and close a new
  window to handle the change instead of requiring all pages to handle change position requests.
*/
function close_window() {
	echo "<script language='JavaScript' type='text/javascript'>
  		<!--
    		self.close();
  		// -->
	</script>\n";
}

/* Clears the session information and redirects to the login page */

function logout() {
	session_unset();
	session_destroy();
	redirect('login.php');
}

/* Checks whether the user in the current session is allowed to log in at all */

function login_allowed() {
//	$permissions = get_permissions();
//	if (check_supervisor() || $permissions['log_in']=="true")
//	{ 
		return true; 
//	}
//	else 
//	{ 
//		return false; 
//	}
}

?>

