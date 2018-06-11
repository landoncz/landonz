<?php
//------------------------------------------------------------------
// Copyright 2007 Holley Grove Software Designs, All Rights Reserved
// Licensed explicitly and soley to Transaction Solutions, LLC for
// unlimited use.
// support@holleygrove.com
require( "lib/auth_user.php" );

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "Transaction Solutions Administration - Account Preferences";
$header  = file_get_contents( "html/main_header_admin.html" );
$content = file_get_contents( "html/edit_account.html"       );
$footer  = file_get_contents( "html/main_footer.html" );

if ( strlen(trim($_GET["action"])) > 0 ) $action = $db->escape(trim($_GET["action"]));
if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_POST["password"])) > 0 ) $password = $db->escape(trim($_POST["password"]));
if ( strlen(trim($_POST["passwordConfirm"])) > 0 ) $passwordConfirm = $db->escape(trim($_POST["passwordConfirm"]));

if ( !empty( $action ) && $action == "do_edit" ) {
	if ( empty( $password ) || empty( $passwordConfirm )) {
		$message = "Error, passwords can not be empty.<br>\n";
	} else if (( strlen( $password ) < 4 ) || ( strlen( $passwordConfirm ) < 4 )) {
		$message = "Error, passwords must be at least 4 characters in length.<br>\n";
	} else if ( $password != $passwordConfirm ) {
		$message = "Error, passwords do not match.<br>\n";
	} else {
		// Success, do the update!
		$query  = "UPDATE admin SET password='" . md5( $password ) . "' WHERE email='";
		$query .= $db->escape( $_SESSION["email"] ) . "' LIMIT 1";
		$db->query( $query );
		$message = "Password updated successfully!<br>\n";
	}
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title     );
$page->SetParameter( "JAVASCRIPT",  $script    );
$page->SetParameter( "PAGE_HEADER", $header    );
$page->SetParameter( "PAGE_CONTENT",$content   );
$page->SetParameter( "ONLOAD_EVENT",$onload    );
$page->SetParameter( "EMAIL",       $_SESSION["user_id"]);
$page->SetParameter( "MESSAGE",     $message   );
$page->SetParameter( "PAGE_FOOTER", $footer    );
$page->SetParameter( "MAIN_SITE",   $site->home_dir  );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
