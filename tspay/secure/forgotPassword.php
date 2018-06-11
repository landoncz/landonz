<?php
// ---------------------------------------------------------------
/* Following section should be included on every page */
// Copyright 2006 Holley Grove Software Designs, All Rights Reserved
// Include files
require( "../lib/tspay.inc.php" );

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "Transaction Solutions, LLC Retrieve Password";
$onload  = "javascript:bringFocus(0,1);";
$login_error = $db->escape(trim($_GET["error"]));
$script  = file_get_contents( "js/focus.js"              );
$header  = file_get_contents( "html/main_header.html"    );
$content = file_get_contents( "html/forgotPassword.html" );
$footer  = file_get_contents( "html/main_footer.html"    );
// ---------------------------------------------------------------
//
$array1 = array( "mark", "dog", "park", "life", "calm", "echo", "grad", "see", "hap" );
$array2 = array( "red", "black", "down", "race", "vid", "jump", "cart", "done", "lift" );

if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_POST["success"])) > 0 ) $success = intval($db->escape(trim($_POST["success"])));

if ( isset( $action ) && $action=="continue" ) {
	$my_user = $db->escape(trim($_POST["user_id"]));
	$check = "SELECT COUNT(email) FROM users WHERE email='" . $my_user . "'";
	$exists = $db->get_var( $check );
	if ( $exists ) {
		// Do the stuff
		$rand1 = rand( 0, 10 );
		$rand2 = rand( 0, 10 );
		$rand3 = rand( 0, 10 );
		$new_pass = $array1[$rand1] . strval( $rand2 * $rand3 + $rand1 ) . $array2[$rand3];
		
		// Update the database
		$update  = "UPDATE users SET password='" . md5( $new_pass ) . "' WHERE ";
		$update .= "email='" . $my_user . "' LIMIT 1";
		if ( !$db->query( $update )) {
			$message = urlencode( "Error Updating the Database" );
			header( "Location: " . $_SERVER["PHP_SELF"] . "?error_message=" . $message );
		}
		
		// Send the email
		$to = $my_user;
		$from_name = "Transaction Solutions";
		$from_address = "admin@tspay.com";
		$subject = "Your TSPay.com Account";
		$body  = "<p>Below is your new password for your TSPay.com account:</p>";
		$body .= "<strong>" . $new_pass . "</strong><p>You may use your new password to ";
		$body .= '<a href="' . $site->secure_dir . '/index.php">login</a> right away.</p>';
		$body .= "<p>If you have any more questions or need further assistance, please ";
		$body .= '<a href="' . $site->home_dir . '/contact.php">contact us</a>.</p>';
		$body .= '<p><br>Thank you for visiting Transaction Solutions!</p>';
		if ( $site->hg_mail( $to, $from_name, $from_address, $subject, $body )) {
			$message = urlencode( "Password Changed Successfully!<br>Your new password has been sent to the email address you provided." );
			header( "Location: " . $_SERVER["PHP_SELF"] . "?good_message=" . $message );
		} else {
			$message = urlencode( "Could not send the email" );
			header( "Location: " . $_SERVER["PHP_SELF"] . "?error_message=" . $message );
		}
	} else {
		// User doesn't exist!
		$message = urlencode( "Email address invalid or does not exist" );
		header( "Location: " . $_SERVER["PHP_SELF"] . "?error_message=" . $message );
	}
}

$form_html = '<form action="' . $_SERVER["PHP_SELF"] . '" method="POST">';

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title     );
$page->SetParameter( "JAVASCRIPT",  $script    );
$page->SetParameter( "PAGE_HEADER", $header    );
$page->SetParameter( "PAGE_CONTENT",$content   );
$page->SetParameter( "ONLOAD_EVENT",$onload    );
$page->SetParameter( "NAV_LINKS",   $nav_links );
$page->SetParameter( "CART",        $cart      );
$page->SetParameter( "FORM_HTML",   $form_html );
$page->SetParameter( "PAGE_FOOTER", $footer    );
$page->SetParameter( "MAIN_SITE",   $site->home_dir  );
$page->SetParameter( "SECURE_SITE", $site->checkout_dir );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
