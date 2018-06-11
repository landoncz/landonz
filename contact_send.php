<?php

if ( strlen(trim($_POST["email"])) > 0 ) $email = stripslashes(trim($_POST["email"]));
if ( strlen(trim($_POST["message"])) > 0 ) $message = stripslashes(trim($_POST["message"]));
if ( strlen(trim($_POST["name"])) > 0 ) $name = stripslashes(trim($_POST["name"]));

if ( isset( $message )) {
	$message = strip_tags( trim( $message ));
	$message = str_replace( "\\r\\n", "<br>", $message );
	$message = str_replace( "\\n", "<br>", $message );
	$message = str_replace( "\\r", "<br>", $message );
	//$to = "landon@holleygrove.com, info@stjohnsdb.com";
	$to = "landonz@gmail.com";
	$from_name = "LandonZ Website - " . $name;
	$from_email = "donotreply@landonz.com";
	$subject = "LandonZ Contact Form";
	$body  = "<p>The following is a user-submitted email from LandonZ.com, ";
	$body .= "please do not reply.</p><p>Message submitted from " . $email;
	$body .= ".</p><p>" . $message . "</p>";
	$body .= "<p>IP: " . $_SERVER["REMOTE_ADDR"] . "</p>";
	hg_mail( $to, $from_name, $from_email, $subject, $body, 0 );
}

header( "Location: http://landonz.com/contact.php?success=1");

function hg_mail( $to, $from_name, $from_address, $subject, $body, $check_errors=0 )
	{
		// First check to make sure it is not a bogus to address
		if ( !((stristr( $to, '@test.com' ) === FALSE ) &&
					(stristr( $to, '@testing.com' ) === FALSE ) &&
					(stristr( $to, '@none.com' ) === FALSE ) &&
					(stristr( $to, 'nobody@' ) === FALSE ) &&
					(stristr( $to, 'noone@' ) === FALSE ) &&
					(stristr( $to, 'donotreply@' ) === FALSE ) &&
					(stristr( $to, 'test@' ) === FALSE ))) {
						return 0;
		}
		
		// Make sure input variables are nice and pretty
		$to           = trim( $to );
		$from_name    = trim( $from_name );
		$from_address = trim( $from_address );
		$subject      = trim( $subject );
		$body         = trim( $body );

		// Is the OS Windows or Mac or Linux???
		if ( strtoupper(substr(PHP_OS,0,3)=='WIN') ) {
			$eol="\r\n";
		} else if ( strtoupper(substr(PHP_OS,0,3)=='MAC') ) {
			$eol="\r";
		} else {
			$eol="\n";
		}
		
		// Common Headers
		$headers .= 'From: ' . $from_name . ' <' . $from_address . '>' . $eol;
		$headers .= 'Reply-To: ' . $from_name . ' <' . $from_address . '>' . $eol;
		$headers .= 'Return-Path: ' . $from_name . ' <' . $from_address . '>' . $eol;
		$headers .= "Message-ID: <" . $now . " TheSystem@" . $_SERVER['SERVER_NAME'] . ">" . $eol;
		// These two to help avoid spam-filters
		$headers .= "X-Mailer: PHP v" . phpversion() . $eol;
		// Boundry for marking the split & Multitype Headers
		$headers .= 'MIME-Version: 1.0' . $eol;
		$headers .= "Content-Type: text/html; charset=ISO-8859-1;" . $eol;
		$headers .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
		
		// BEGIN Message
		$msg  = "";
		$msg .= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional">
	<html>
	<head>
	  <meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
	  <title></title>
	</head>
	<body bgcolor="#ffffff" text="#000000">
	';
		$msg .= $body . $eol;
		$msg .= "</body></html>";
		
		// SEND THE EMAIL
		// INI lines are to force the From Address to be used !
		ini_set( sendmail_from, $from_address );
		$response = mail( $to, $subject, $msg, $headers );
		ini_restore( sendmail_from );
		
		// Play nice and unset variables
		unset( $to );
		unset( $from_name );
		unset( $from_address );
		unset( $subject );
		unset( $body );
		unset( $msg );
		unset( $headers );
		return $response;
	}
?>