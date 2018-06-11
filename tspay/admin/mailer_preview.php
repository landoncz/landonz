<?php
//-----------------------------------------------------
// Page will show a preview of an email from the database
require( "lib/auth_user.php" );

if ( strlen(trim($_GET["id"])) > 0 ) {
	$email_id = $db->escape(trim($_GET["id"]));
} else {
	if ( strlen(trim($_POST["id"])) > 0 ) {
		$email_id = $db->escape(trim($_POST["id"]));
	} else {
		$email_id = 0;
	}
}
//--------------------------------------

// -------------------------------------
// Begin Execution
if ( !$email_id ) {
	// No id passed
} else {
	$query  = "SELECT * FROM news_letters WHERE id='";
	$query .= $email_id . "' LIMIT 1";
	$email = $db->get_row( $query );
	
	/*
	echo "<center><h2>Subject: ";
	echo stripslashes( $email->subject );
	echo "</h2></center>";
	*/
	echo stripslashes( $email->body );
}

