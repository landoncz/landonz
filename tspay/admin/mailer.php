<?php
//------------------------------------------------------------------
// Copyright 2007 Holley Grove Software Designs, All Rights Reserved
// Licensed explicitly and soley to Transaction Solutions, LLC for
// unlimited use.
// support@holleygrove.com
require( "lib/auth_user.php" );
include( "lib/FCKeditor/fckeditor.php");

// Set the timeout so this script doesn't time out
set_time_limit(0);

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "Transaction Solutions Administration - HG News Mailer";
$header  = file_get_contents( "html/main_header_admin.html" );
$content = file_get_contents( "html/mailer.html"            );
$footer  = file_get_contents( "html/main_footer.html"       );

if ( strlen(trim($_GET["action"])) > 0 ) $action = $db->escape(trim($_GET["action"]));
if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_GET["id"])) > 0 ) $email_id = $db->escape(trim($_GET["id"]));
if ( strlen(trim($_POST["id"])) > 0 ) $email_id = $db->escape(trim($_POST["id"]));

if ( $action == "mail" ) {
	// Get the email ID and make sure it has not already been sent
	if ( !$email_id ) {
		// Error getting the ID
		$message = "Error, email not sent!<br>\n";
	} else {
		$check = "SELECT hasBeenSent FROM news_letters WHERE id='";
		$check .= $email_id . "' LIMIT 1";
		$already_mailed = $db->get_var( $check );
		$already_mailed = 0;
		if ( $already_mailed ) {
			// Error, don't let them mail it twice
			$message = "Error, email has already been sent, email cannot be sent more than once!<br>\n";
		} else {
			// Mark the email as sent
			$update  = "UPDATE news_letters SET hasBeenSent=1, ";
			$update .= "sentDate=NOW() WHERE id='";
			$update .= $email_id . "' LIMIT 1";
			$db->query( $update );
			
			// Get the email subject and body
			$details  = "SELECT subject, body FROM news_letters WHERE ";
			$details .= "id='" . $email_id . "' LIMIT 1";
			$detail = $db->get_row( $details );
			
			// Pull email list of users from the database
			$select_users  = "SELECT email FROM users WHERE sendOffers AND available";
			$users = $db->get_results( $select_users );
			
			// Loop through the users and email each one
			foreach( $users as $user )
			{
				// Send the mail
				if ( $site->hg_mail( $user->email, "Transaction Solutions", 
					"sales@tspay.com", stripslashes($detail->subject),
					stripslashes($detail->body), 0 )) {
					$message .= "Sent to " . $user->email . " sucessfully<br>\n";
				} else {
					$message .= "Sent to " . $user->email . " FAILED<br>\n";
				}
			}
		}
	}
} else if ( $action == "add" ) {
	$message = "Add a new email";
	$content = file_get_contents( "html/add_email_form.html" );
} else if ( $action == "update" ) {
	// Run the update query and send to the appropriate preview page
	$update  = "UPDATE news_letters SET subject='";
	$update .= $db->escape( $_POST["subject"] );
	$update .= "', body='" . $db->escape( $_POST["body"] );
	$update .= "' WHERE id='" . $email_id . "' LIMIT 1";
	$db->query( $update );
	
	$message = "<strong><center>Update Successful</center></strong>";
} else if ( $action == "edit" ) {
	// Allow them to edit an email (as long as it hasnt been sent)
	if ( !$email_id ) {
		// Error getting the ID
		$message = "Error, email not sent!<br>\n";
	} else {
		$check = "SELECT hasBeenSent FROM news_letters WHERE id='";
		$check .= $email_id . "' LIMIT 1";
		$already_mailed = $db->get_var( $check );
		if ( $already_mailed ) {
			// Error, don't let them mail it twice
			$message = "Error, email has already been sent, email cannot be sent more than once!<br>\n";
		} else {
			// All clear, get email details and print the edit form
			$details  = "SELECT subject, body FROM news_letters WHERE ";
			$details .= "id='" . $email_id . "' LIMIT 1";
			$detail = $db->get_row( $details );
			
			$message = "Edit Email<br>\n";
			
			$email_html  = "<p class='normalText' align='center'><strong>Paste your email";
			$email_html .= " html code in the form below. You will be able to preview";
			$email_html .= " your email on the next page.</strong></p>\n";
			$email_html .= '<form action="';
			$email_html .= $_SERVER["PHP_SELF"];
			$email_html .= '" method="POST">';
			$email_html .= "\n";
			$email_html .= '<input type="hidden" name="action" value="update">';
			$email_html .= "\n";
			$email_html .= '<input type="hidden" name="id" value="';
			$email_html .= $email_id;
			$email_html .= 'update">';
			$email_html .= "\n";
			$email_html .= '<table border="0" cellpadding="7" align="center" width="95%">';
			$email_html .= "\n";
			$email_html .= "<tr>\n<td width='100'><strong>Subject</strong></td>\n";
			$email_html .= '<td><input type="text" name="subject" size="60" ';
			$email_html .= 'value="';
			$email_html .= stripslashes($detail->subject);
			$email_html .= '" maxlength="150"></td>';
			$email_html .= "\n</tr>\n<tr>\n<td><strong>HTML body</strong></td>\n";
			$email_html .= '<td>';
			$oFCKeditor = new FCKeditor('body');
			$oFCKeditor->BasePath = 'lib/FCKeditor/';
			$oFCKeditor->Width  = '100%';
			$oFCKeditor->Height = '400';
			$oFCKeditor->Value  = $detail->body;
			$email_html .= $oFCKeditor->CreateHtml();
			//<textarea name="body" style="width: 100%; height: 400px;">';
			//echo stripslashes($detail->body);
			//echo '</textarea></td>';
			$email_html .= '</td>';
			$email_html .= "</tr>\n<tr><td colspan='2' align='center'><br>";
			$email_html .= '<input type="submit" value="Update Email">';
			$email_html .= "</td>\n</tr>\n</table>\n";
			$email_html .= "<br><center>\n<a href='";
			$email_html .= $_SERVER["PHP_SELF"];
			$email_html .= "'>Cancel</a>\n<br>\n";
		}
	}
} else if ( $action == "preview" ) {
	$message = "Your Email<br>\n";
	
	// Give them a preview of the email in a new page
	$email_html  = '<iframe name="email_preview" src="mailer_preview.php?id=';
	$email_html .= $email_id . '" width="100%" height="400" frameborder="1" scrolling="yes">';
	$email_html .= "\n</iframe>\n<br>\n";
	
	// Give them the mail button or the edit button
	$email_html .= "<center>\n<br>\n<a href='" . $_SERVER["PHP_SELF"];
	$email_html .= "?action=mail&id=" . $email_id;
	$email_html .= "'>Send Email</a>\n<br>\n</center>\n";
} else if ( $action == "preview_first" ) {
	$message = "Your Email<br>\n";
	
	// Go ahead and insert the email into the database
	$insert  = "INSERT INTO news_letters ( subject, body, htmlBody ) VALUES ( '";
	$insert .= $db->escape( $_POST["subject"] ) . "', '";
	$insert .= $db->escape( $_POST["body"] ) . "', '";
	$insert .= $db->escape( $_POST["htmlBody"] ) . "' )";
	$db->query( $insert );
	$email_id = $db->insert_id;
	
	// Give them a preview of the email in a new page
	$email_html  = '<iframe name="email_preview" src="mailer_preview.php?id=';
	$email_html .= $email_id . '" width="100%" height="400" frameborder="1" scrolling="yes">';
	$email_html .= "\n</iframe>\n<br>\n";
	
	// Give them the mail button or the edit button
	$email_html .= "<center>\n<br>\n<a href='" . $_SERVER["PHP_SELF"];
	$email_html .= "?action=mail&id=" . $email_id;
	$email_html .= "'>Send Email</a>\n<br>\n</center>\n";
} else if ( $action == "preview_nosend" ) {
	$message = "Your Email<br>\n";
	
	// Give them a preview of the email in a new page
	$email_html  = '<iframe name="email_preview" src="mailer_preview.php?id=';
	$email_html .= $email_id . '" width="100%" height="400" frameborder="1" scrolling="yes">';
	$email_html .= "\n</iframe>\n<br>\n";
} else {
	$message = "Current Emails<br>\n";
	$content = file_get_contents( "html/list_emails.html" );
	
	// Show all the past emails and allow them to add a new one
	$emails = $db->get_results( "SELECT * from news_letters ORDER by composeDate DESC" );
	
	$first = 1;
	// Loop through all the emails and print them out in a table
	foreach ( $emails as $email )
	{
		$email_html .= "<tr>\n";
		if ( $email->hasBeenSent ) {
			$email_html .= '<td bgcolor="#DDDDDD" class="normalText" align="left">';
			$email_html .= $email->composeDate;
			$email_html .= "</td>\n";
			$email_html .= '<td bgcolor="#DDDDDD" class="normalText" align="left">';
			$email_html .= $email->subject;
			$email_html .= "</a></td>\n";
			$email_html .= '<td bgcolor="#DDDDDD" class="normalText" align="left">';
			$email_html .= "Yes";
			$email_html .= "</td>\n";
			$email_html .= '<td bgcolor="#DDDDDD" class="normalText" align="left">';
			$email_html .= $email->sentDate;
			$email_html .= "</td>\n";
			$email_html .= '<td bgcolor="#DDDDDD" class="normalText" align="left">';
			$email_html .= "<a href='";
			$email_html .= "?action=preview_nosend&id=";
			$email_html .= $email->id;
			$email_html .= "'>";
			$email_html .= "Preview</a></td>\n";
		} else {
			$email_html .= '<td class="normalText" align="left">';
			$email_html .= $email->composeDate;
			$email_html .= "</td>\n";
			$email_html .= '<td class="normalText" align="left">';
			$email_html .= $email->subject;
			$email_html .= "</a></td>\n";
			$email_html .= '<td class="normalText" align="left">';
			$email_html .= "No";
			$email_html .= "</td>\n";
			$email_html .= '<td class="normalText" align="left">';
			$email_html .= $email->sentDate;
			$email_html .= "</td>\n";
			$email_html .= '<td class="normalText" align="left">';
			$email_html .= "<a href='";
			$email_html .= $_SERVER["PHP_SELF"];
			$email_html .= "?action=edit&id=";
			$email_html .= $email->id;
			$email_html .= "'>";
			$email_html .= "Edit</a> | <a href='";
			$email_html .= "?action=preview&id=";
			$email_html .= $email->id;
			$email_html .= "'>";
			$email_html .= "Preview</a></td>\n";
		}
		$email_html .= "</tr>\n";
	}
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",    $title     );
$page->SetParameter( "JAVASCRIPT",    $script    );
$page->SetParameter( "PAGE_HEADER",   $header    );
$page->SetParameter( "PAGE_CONTENT",  $content   );
$page->SetParameter( "ONLOAD_EVENT",  $onload    );
$page->SetParameter( "MESSAGE",       $message   );
$page->SetParameter( "EMAIL_HTML",    $email_html);
$page->SetParameter( "PAGE_FOOTER",   $footer    );
$page->SetParameter( "MAIN_SITE",     $site->home_dir  );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
