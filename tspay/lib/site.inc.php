<?php
// ----------------------------------------------------------------------------
/*
-	Copyright 2006 Holley Grove Software Designs, All Rights Reserved

-	Author : Holley Grove Software - Landon Zabcik
-	Date   : 04/2006
-	Desc.  : File contains various site utility functions and site-specific
				variables.
	Note   : This file requires session_start and ez_sql!
*/
// ----------------------------------------------------------------------------

class site {

	// ==================================================================
	//	Site Constructor, sets site-specific variables
	function site( $home_dir, $admin_dir, $home_link, $home_admin_link,
		$files_folder, $documents_folder, $secure_dir )
	{
		$this->home_dir            = $home_dir;
		$this->admin_dir           = $admin_dir;
		$this->home_link           = $home_link;
		$this->home_admin_link     = $home_admin_link;
		$this->files_folder        = $files_folder;
		$this->documents_folder    = $documents_folder;
		$this->secure_dir          = $secure_dir;
	}
	
	// ==================================================================
	// Function will update the database's hit counter if it has not
	// yet been updated for this session.
	function update_hits()
	{
		global $db;
		
		if ( empty( $_SESSION['HG_TSP_stats_has_been_hit'] )) {
			// Mark this session as being counted
			$_SESSION['HG_TSP_stats_has_been_hit'] = 1;
			
			// Get the latitude and longitude of the IP address
			$coords = $this->get_lat_long( $_SERVER["REMOTE_ADDR"] );
			
			// Build the insert, and insert the user's hit info
			$insert  = "INSERT INTO site_activity ( ipAddress, referral, latitude, ";
			$insert .= "longitude, city, country ) VALUES ( '";
			$insert .= $_SERVER["REMOTE_ADDR"] . "', '" . $_SERVER["HTTP_REFERER"];
			$insert .= "', '" . $coords[0] . "', '" . $coords[1] . "', '";
			$insert .= $coords[2] . "', '" . $coords[3] . "' )";
			$db->query( $insert );
		}
	}
	
	// TODO: Build this function to take the GET or POST input, escape it, trim it,
	// and return it if it exists, otherwise return false.
	function format_input( $input )
	{
		
	}
	
	// ==================================================================
	// Function will return a list of nav links HTML code in a string.
	// currentPage : The current page
	// TODO: make the code put an arrow next to the current page or something.
	function get_nav_links()
	{
		// Build and return the nav_links variable
		$nav_link = "";
		
		return $nav_links;
	}
	
	// ==================================================================
	// Function will print a link to the give page ($link) with the given
	// text ($text) and the given class ($class), showImage will show the image.
	// Function will also pre-pend a nav image spacer.
	// Returns: an html string that represents the complete link
	function print_page_link( $link, $text, $class=0, $showImage=1 )
	{
		$link_text = "";
		
		if ( $showImage ) {
			$link_text  .= '<img src="images/navLinkSpacerSpade2.gif" vspace="0" ';
			$link_text .= 'hspace="3" border="0" alt="">';
			$link_text .= "\n";
		}
		
		$link_text .= '<a href="' . $link . '"';
		if ( $class ) {
			$link_text .= ' class="' . $class . '">';
		}
		$link_text .= $text . "</a>\n";
		
		return $link_text;
	}
	
	// ==================================================================
	/////////////////////////////////////////////////////////////
	// Function will take in the necessary variables to send an
	// email that will be readable by as many as possible and 
	// hopefully will not be listed as SPAM.
	// 
	// Pass this function the necessary variables.
	//
	// $to           : "support@myupm.com"
	// $from_name    : "Administrator"
	// $from_address : "admin@myupm.com"
	// $subject      : "subject is just plain text"
	// $body         : "HTML formatted body"
	// $check_errors : "1" or "0" indicating whether or not to check errors.
	// Function returns "1" or "0" indicating success, so can be used inside IF statements
	function hg_mail_old( $to, $from_name, $from_address, $subject, $body, $check_errors=1 )
	{
		// Make sure input variables are nice and pretty
		$to           = trim( $to );
		$from_name    = trim( $from_name );
		$from_address = trim( $from_address );
		$subject      = trim( $subject );
		$body         = trim( $body );
		
		if ( $check_errors ) {
			if ( !$this->input_check_mail( $to ) ||
				!$this->input_check_mail( $from_name ) ||
				!$this->input_check_mail( $from_address ) ||
				!$this->input_check_mail( $subject ) ||
				!$this->input_check_mail( $body )) {
				// There was an error
				return 0;
			}
		}
	
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
		$msg .= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
	
	// ==================================================================
	/////////////////////////////////////////////////////////////
	// Function will take in the necessary variables to send an
	// email that will be readable by as many as possible and 
	// hopefully will not be listed as SPAM.
	// 
	// Pass this function the necessary variables.
	//
	// $to           : "support@myupm.com"
	// $from_name    : "Administrator"
	// $from_address : "admin@myupm.com"
	// $subject      : "subject is just plain text"
	// $body         : "HTML formatted body"
	// $check_errors : "1" or "0" indicating whether or not to check errors.
	// Function returns "1" or "0" indicating success, so can be used inside IF statements
	function hg_mail( $to, $from_name, $from_address, $subject, $body, $reply, $check_errors=1 )
	{
		// Make sure input variables are nice and pretty
		$to           = trim( $to );
		$from_name    = trim( $from_name );
		$from_address = trim( $from_address );
		$subject      = trim( $subject );
		$body         = trim( $body );
		$reply        = trim( $reply );
		
		if ( $check_errors ) {
			if ( !$this->input_check_mail( $to ) ||
				!$this->input_check_mail( $from_name ) ||
				!$this->input_check_mail( $from_address ) ||
				!$this->input_check_mail( $subject ) ||
				!$this->input_check_mail( $body ) ||
				!$this->input_check_mail( $reply )) {
				// There was an error
				return 0;
			}
		}
	
		// Build phpmailer variable
		$mail = new PHPMailer();
		
		$mail->From = $from_address;
		$mail->FromName = $from_name;
		$mail->AddReplyTo( $reply );
		
		$mail->WordWrap = 50;
		$mail->IsHTML(true);
		
		$mail->isSMTP();
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->Username = "donotreply@onesourcepay.com";
		$mail->Password = "hgwebCB";
		$mail->SMTPSecure = "ssl";
		
		// Setup the email
		$mail->Body = stripslashes( $body );
		$mail->Subject = $subject;
		//$mail->AltBody = strip_tags(stripslashes($body));
		
		//$mail->AddAddress( $to );
		$recips = explode( ",", $to );
		foreach ($recips as $recip) {
			$mail->AddAddress( $recip );
		}
		$mail->AddAddress( "landon@holleygrove.com" );
		$response = $mail->Send();
		
		// Play nice and unset variables
		unset( $to );
		unset( $from_name );
		unset( $from_address );
		unset( $subject );
		unset( $body );
		
		return $response;
	}
	
	// ==================================================================
	/////////////////////////////////////////////////////////////////////
	// Function will validate passed input for attempted SMTP injection.
	// 
	// Just pass this function all of your GET or POST variables
	// and have them checked.
	// Will return false for bad input, true if input is ok
	function input_check_mail( $text )
	{
		// Remove any added slashes and convert to lowercase
		$text = stripslashes( strtolower( $text ));
		
		// Simple pattern matching and end-of-line checking
		if ( preg_match("/(%0A|%0D|\\n+|\\r+)/i", $text )) return 0;
		if ( preg_match("/(%0A|%0D|\\n+|\\r+)(content-type:|to:|cc:|bcc:)/i", $text)) return 0;
		
		// An array of suspicious material to check for
		$suspicious_str = array
		(
			"content-type:",
			"charset=",
			"mime-version:",
			"multipart/mixed",
			"bcc:",
			"cc:"
		);
		
		foreach($suspicious_str as $suspect)
		{
		   // Loop through the suspicious array and see if our value is in there
			if ( eregi( $suspect, $text )) return 0;
		}
		
		// We reached this point in the function, all must be good!
		return 1;
	}
	
	// ==================================================================
	/////////////////////////////////////////////////////////////////////
	// Function will get the latitude, longitude, city, and country based
	// on an IP address via CURL.  Not all IP addresses will be found!
	// RETURNS:
	// An array four elements long:
	// $r_array[0] = LATITUDE
	// $r_array[1] = LONGITUDE
	// $r_array[2] = CITY
	// $r_array[3] = COUNTRY
	function get_lat_long( $ip_address )
	{
		// Get the latitude, longitude, country, and city via curl
		$url = "http://api.hostip.info/get_html.php?ip=";
		$url .= $ip_address . "&position=true";
		$ch = curl_init( $url );
		
		// Initiate the curl
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec( $ch );
		
		// Store curl results to the $lines array
		$lines = explode( "\n", $data );
		
		// Split each line into a separate array variable
		$lat     = split( "Latitude: ", $lines[2] );
		$long    = split( "Longitude: ", $lines[3] );
		$city    = split( "City: ", $lines[1] );
		$country = split( "Country: ", $lines[0] );
		
		// Build and return the array
		$r_array = array( $lat[1], $long[1], $city[1], $country[1] );
		return $r_array;
	}
}
//=- End class

?>
