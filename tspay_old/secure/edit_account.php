<?php
// ---------------------------------------------------------------
/* Following section should be included on every page */
// Copyright 2007 Holley Grove Software Designs, All Rights Reserved
// Include files
require( "lib/auth_user.php" );

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "Transaction Solutions, LLC";
$onload  = "";
$header  = file_get_contents( "html/main_header.html" );
$content = file_get_contents( "html/edit_account.html");
$footer  = file_get_contents( "html/main_footer.html" );

if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));

if ( !empty( $action ) && $action == "updatePassword" ) {
	$new_pass         = trim( $db->escape(trim($_POST["password"])));
	$new_pass_confirm = trim( $db->escape(trim($_POST["passwordConfirm"])));
	if ( !empty($new_pass) && !empty($new_pass_confirm) ) {
		if ( $new_pass == $new_pass_confirm ) {
			// Passwords match, now check to make sure they meet minimum
			// length and character requirements
			if ( eregi ("^[[:alnum:]]{6,20}$", $new_pass)) {
				// All is good!  Change the password
				$update  = "UPDATE users SET password='" . md5( $new_pass );
				$update .= "' WHERE email='" . $_SESSION["HGTS_AGENT_email"] . "' LIMIT 1";
				if ( !$db->query( $update )) {
					$login_error  = "*** Error updating our database ***<br>\n";
				}
			} else {
				$login_error  = "*** Invalid new password ***<br>\n";
				$login_error .= "(Can only be letters and numbers, 6-20 characters)";
			}
		} else {
			$login_error = "*** Passwords do not match ***";
		}
	} else {
		// User tried to continue with some blank fields! Bad user!
		$login_error = "*** No fields can be left empty ***";
	}
	
	if ( !empty( $login_error )) {
		// There was a problem, send them back
		$new_page = $_SERVER["PHP_SELF"] . "?error_message=" . urlencode( $login_error );
		header( "Location: " . $new_page );
		exit;
	} else {
		$message = "Your password was changed successfully.";
		$new_page = $_SERVER["PHP_SELF"] . "?good_message=" . urlencode( $message );
		header( "Location: " . $new_page );
		exit;
	}
} else if ( !empty( $action ) && $action == "updateOffers" ) {
	// User wants to change their offer preferences
	$change = intval( $_POST["sendOffers"] );
	$query  = "UPDATE users SET sendOffers='" . $change . "' WHERE email='";
	$query .= $_SESSION["HGTS_AGENT_email"] . "' LIMIT 1";
	$db->query( $query );
	
	$message = "Your email preferences have been successfully changed.";
	$new_page = $_SERVER["PHP_SELF"] . "?good_message=" . urlencode( $message );
	header( "Location: " . $new_page );
	exit;
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title            );
$page->SetParameter( "JAVASCRIPT",  $script           );
$page->SetParameter( "PAGE_HEADER", $header           );
$page->SetParameter( "PAGE_CONTENT",$content          );
$page->SetParameter( "ONLOAD_EVENT",$onload           );
$page->SetParameter( "NAV_LINKS",   $nav_links        );
$page->SetParameter( "EMAIL",       $_SESSION["HGTS_AGENT_email"]);
$page->SetParameter( "PAGE_FOOTER", $footer           );
$page->SetParameter( "MAIN_SITE",   $site->home_dir   );
$page->SetParameter( "SECURE_SITE", $site->secure_dir );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
