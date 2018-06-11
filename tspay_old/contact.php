<?php
// ---------------------------------------------------------------
/* Following section should be included on every page */
// Copyright 2007 Holley Grove Software Designs, All Rights Reserved
// Licensed exclusively to Transaction Solutions, LLC.
// Include files
require( "lib/tspay.inc.php" );

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "One Source Payments - Contact Us";
$header  = file_get_contents( "html/header_contact.html" );
$content = file_get_contents( "html/contact.html"        );
$footer  = file_get_contents( "html/main_footer.html"    );
$script  = file_get_contents( "js/focus.js"              );
$script .= file_get_contents( "js/google_maps.js"        );
$script .= file_get_contents( "js/contact.js"            );
$onload  = 'javascript:bringFocus( 0,0 ); load()" onunload="GUnload()';

// Check and format GET variables if any
if ( strlen(trim($_GET["action"])) > 0 ) $action = $db->escape(trim($_GET["action"]));
if ( strlen(trim($_GET["success"])) > 0 ) $success = $db->escape(trim($_GET["success"]));
if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_POST["from_email"])) > 0 ) $from_email = $db->escape(trim($_POST["from_email"]));
if ( strlen(trim($_POST["phone_number"])) > 0 ) $phone_number = $db->escape(trim($_POST["phone_number"]));
if ( strlen(trim($_POST["message"])) > 0 ) $message = $db->escape(trim($_POST["message"]));
if ( strlen(trim($_POST["subject"])) > 0 ) $subject = $db->escape(trim($_POST["subject"]));

// User is trying to send the mail
if (( isset( $action )) && ( $action == "mail" )) {
	if (( strlen( $from_email ) < 10 ) || ( strlen( $message ) < 10 )) {
		// Something was missing, don't try to deliver
		header( "Location: " . $_SERVER["PHP_SELF"] . "?success=2" );
		exit;
	}
	
	// Ok, go ahead and mail it!
	$message = strip_tags( $message );
	$message = str_replace( "\\r\\n", "<br>", $message );
	$message = str_replace( "\\n", "<br>", $message );
	$message = str_replace( "\\r", "<br>", $message );
	$to = "jgodin@onesourcepay.com,mfair@onesourcepay.com,fvancamp@onesourcepay.com";
	//$to = "mfair@tspay.com,landon@holleygrove.com";
	$from_name = "onesourcepay.com";
	$from_address = "donotreply@onesourcepay.com";
	$body  = "<p>The following is a user-submitted email from onesourcepay.com, ";
	$body .= "please do not reply.</p><p>Message submitted from " . $from_email;
	$body .= " and phone number: " . $phone_number;
	$body .= ".</p><p>" . $message . "</p>";
	if ( $site->hg_mail( $to, $from_name, $from_address, $subject, $body )) {
		header( "Location: " . $_SERVER["PHP_SELF"] . "?success=1" );
		exit;
	} else {
		header( "Location: " . $_SERVER["PHP_SELF"] . "?success=2" );
		exit;
	}
}

// Mail was sent, check the status and display the message
if (( isset( $success )) && ( $success == 1 )) {
	// The mail was a success, print the successfull message
	$S_message = "Your message was delivered successfully!";
} else if (( isset( $success )) && ( $success == 2 )) {
	// Mail was not delievered, possibly a hack attempt
	$S_message  = "There was a problem delivering your message, please contact our ";
	$S_message .= "office directly, we're sorry for the inconvenience.";
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title            );
$page->SetParameter( "JAVASCRIPT",  $script           );
$page->SetParameter( "PAGE_HEADER", $header           );
$page->SetParameter( "PAGE_CONTENT",$content          );
$page->SetParameter( "ONLOAD_EVENT",$onload           );
$page->SetParameter( "SUCCESS_MESSAGE",$S_message     );
$page->SetParameter( "NAV_LINKS",   $nav_links        );
$page->SetParameter( "PAGE_FOOTER", $footer           );
$page->SetParameter( "MAIN_SITE",   $site->home_dir   );
$page->SetParameter( "SECURE_SITE", $site->secure_dir );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
