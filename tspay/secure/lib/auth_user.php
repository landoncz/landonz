<?php
//-----------------------------------------------------
// auth_user.php
// This page will authorize the user for the admin pages
// or kick them out all together with or without an error.

//session_start();
require( "../lib/tspay.inc.php" );

// Redirect to secure site if need be
if ( !($_SERVER["HTTPS"] )) {
	header( "Location: " . $site->secure_dir );
	exit;
}

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "Transaction Solutions Agent Login";
$header  = file_get_contents( "html/main_header.html" );
$content = file_get_contents( "html/login_form.html"  );
$footer  = file_get_contents( "html/main_footer.html" );
$script  = file_get_contents( "js/focus.js"           );
$onload  = "javascript:bringFocus(0,0);";

if ( !isset($_SESSION["HGTS_AGENT_user_id"])) {
	// The session user id is not set, are they trying to login?
	if ( isset($_POST["user_id"]) && isset($_POST["password"]) ) {
		// They are trying to login, check login info
		$username = trim( $db->escape( $_POST["user_id"] ));
		$password = md5( trim( $db->escape( $_POST["password"] )));
		$auth  = "SELECT * FROM users WHERE email ='";
		$auth .= $username . "' and password = '" . $password . "'";
		if ( $my_user = $db->get_row($auth) ) {
			// Login succesfull!  Set session variables
			$_SESSION["HGTS_AGENT_user_id"] = $my_user->email;
			$_SESSION["HGTS_AGENT_email"]   = $my_user->email;
			$message = urlencode( "Login Successful." );
			header( "Location: " . $_SERVER["REQUEST_URI"] . "?good_message=" . $message );
		} else {
			// Login was unsucessful.  Print out error message
			$message  = "Unable to verify user login information ";
			$message .= "in the database.\n<br>\n";
			$message .= "Please check your login information or contact an ";
			$message .= "administrator.\n<br>";
			$show_login = 1;
		}
	} else {
		// They are not trying to login, but need to, give them the login form
		$show_login = 1;
	}
}
$login_form  = '<form action="' . $_SERVER["PHP_SELF"]. '" method="post">';

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title     );
$page->SetParameter( "JAVASCRIPT",  $script    );
$page->SetParameter( "PAGE_HEADER", $header    );
$page->SetParameter( "PAGE_CONTENT",$content   );
$page->SetParameter( "ONLOAD_EVENT",$onload    );
$page->SetParameter( "FORM_HTML",   $login_form);
$page->SetParameter( "ADMIN_MESSAGE",$message  );
$page->SetParameter( "NAV_LINKS",   $nav_links );
$page->SetParameter( "PAGE_FOOTER", $footer    );
$page->SetParameter( "MAIN_SITE",   $site->home_dir  );
$page->SetParameter( "SECURE_SITE", $site->secure_dir);

if ( $show_login ) {	
	// Now send the completed instance of the class to the browser
	$page->CreatePage();
	
	// Die so that no other information is displayed
	die;
} else {
	unset( $page );
}

?>
