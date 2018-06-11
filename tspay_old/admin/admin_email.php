<?php
//-----------------------------------------------------
// Page will allow admin to add a product color to the database
require( "lib/auth_user.php" );
include( "lib/FCKeditor/fckeditor.php");

// -------------------------------------
// Format GET & POST variables first
if ( strlen(trim($_GET["action"])) > 0 ) {
	$action = $db->escape(trim($_GET["action"]));
} else {
	if ( strlen(trim($_POST["action"])) > 0 ) {
		$action = $db->escape(trim($_POST["action"]));
	} else {
		$action = '';
	}
}
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
if ( $action == "mail" ) {
	// Get the email ID and make sure it has not already been sent
	if ( !$email_id ) {
		// Error getting the ID
		
	} else {
		$check = "SELECT hasBeenSent FROM news_letters WHERE id='";
		$check .= $email_id . "' LIMIT 1";
		$already_mailed = $db->get_var( $check );
		if ( $already_mailed ) {
			// Error, don't let them mail it twice
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
			$select_users  = "SELECT email FROM users WHERE sendOffers";
			$users = $db->get_results( $select_users );
			
			// Loop through the users and email each one
			foreach( $users as $user )
			{
				// Send the mail
				$site->hg_mail( $user->email, "Pensacola Blues", "promotions@pensacolablues.com", stripslashes($detail->subject), stripslashes($detail->body), 0);
				echo "Sent to ";
				echo $user->email;
				echo "sucessfully<br>\n";
			}
		}
	}
} else if ( $action == "add" ) {
	// Allow them to add a new email, give them the add form
	echo "<center><h2>Pensacola Blues Email Portal</h2></center>\n";
	echo "<center><h2><br>Add a new email</h2></center>\n";
	echo "<center><p><strong>To add a new email, paste your email";
	echo " html code in the form below. You will be able to preview";
	echo " your email on the next page.</strong></p></center>\n";
	echo '<form action="';
	echo $_SERVER["PHP_SELF"];
	echo '" method="POST">';
	echo "\n";
	echo '<input type="hidden" name="action" value="preview_first">';
	echo "\n";
	echo '<table border="1" width="70%" align="center"><tr><td>';
	echo "\n";
	echo '<table border="0" cellpadding="7" align="center" width="95%">';
	echo "\n";
	echo "<tr>\n<td width='100'><strong>Subject</strong></td>\n";
	echo '<td><input type="text" name="subject" size="60" maxlength="150"></td>';
	echo "\n</tr>\n<tr>\n<td><strong>Email body</strong></td>\n";
	echo '<td><textarea name="body" style="width: 100%; height: 450px;"></textarea></td>';
	echo "</tr>\n";
	echo "\n</tr>\n<tr>\n<td><strong>HTML body</strong></td>\n";
	echo '<td><textarea name="htmlBody" style="width: 100%; height: 450px;"></textarea></td>';
	echo "</tr>\n<tr><td colspan='2' align='center'><br>";
	echo '<input type="submit" value="Preview Email">';
	echo "</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n";
	echo "<br><center>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "'>Cancel</a>\n<br>\n";
} else if ( $action == "edit" ) {
	// Allow them to edit an email (as long as it hasnt been sent)
	if ( !$email_id ) {
		// Error getting the ID
		
	} else {
		$check = "SELECT hasBeenSent FROM news_letters WHERE id='";
		$check .= $email_id . "' LIMIT 1";
		$already_mailed = $db->get_var( $check );
		if ( $already_mailed ) {
			// Error, don't let them mail it twice
		} else {
			// All clear, get email details and print the edit form
			$details  = "SELECT subject, body FROM news_letters WHERE ";
			$details .= "id='" . $email_id . "' LIMIT 1";
			$detail = $db->get_row( $details );
			
			echo "<center><h2>Pensacola Blues Email Portal</h2></center>\n";
			echo "<center><h2><br>Edit email</h2></center>\n";
			echo "<center><p><strong>Paste your email";
			echo " html code in the form below. You will be able to preview";
			echo " your email on the next page.</strong></p></center>\n";
			echo '<form action="';
			echo $_SERVER["PHP_SELF"];
			echo '" method="POST">';
			echo "\n";
			echo '<input type="hidden" name="action" value="update">';
			echo "\n";
			echo '<input type="hidden" name="id" value="';
			echo $email_id;
			echo 'update">';
			echo "\n";
			echo '<table border="1" width="70%" align="center"><tr><td>';
			echo "\n";
			echo '<table border="0" cellpadding="7" align="center" width="95%">';
			echo "\n";
			echo "<tr>\n<td width='100'><strong>Subject</strong></td>\n";
			echo '<td><input type="text" name="subject" size="60" ';
			echo 'value="';
			echo stripslashes($detail->subject);
			echo '" maxlength="150"></td>';
			echo "\n</tr>\n<tr>\n<td><strong>HTML body</strong></td>\n";
			echo '<td>';
			$oFCKeditor = new FCKeditor('body');
			$oFCKeditor->BasePath = 'lib/FCKeditor/';
			$oFCKeditor->Width  = '100%';
			$oFCKeditor->Height = '400';
			$oFCKeditor->Value  = $detail->body;
			$oFCKeditor->Create();
			//<textarea name="body" style="width: 100%; height: 400px;">';
			//echo stripslashes($detail->body);
			//echo '</textarea></td>';
			echo '</td>';
			echo "</tr>\n<tr><td colspan='2' align='center'><br>";
			echo '<input type="submit" value="Update Email">';
			echo "</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n";
			echo "<br><center>\n<a href='";
			echo $_SERVER["PHP_SELF"];
			echo "'>Cancel</a>\n<br>\n";
		}
	}
} else if ( $action == "update" ) {
	// Run the update query and send to the appropriate preview page
	$update  = "UPDATE news_letters SET subject='";
	$update .= $db->escape( $_POST["subject"] );
	$update .= "', body='" . $db->escape( $_POST["body"] );
	$update .= "' WHERE id='" . $email_id . "' LIMIT 1";
	$db->query( $update );
	
	echo "<center><h2>Update Successful</h2>";
	echo '<a href="';
	echo $_SERVER["PHP_SELF"];
	echo '?action=preview&id=';
	echo $email_id;
	echo '">Preview</a>';
} else if ( $action == "preview" ) {
	echo "<center><h2>Pensacola Blues Email Portal</h2></center>\n";
	echo "<center><h2><br>Your Email</h2></center>\n";
	
	// Give them a preview of the email in a new page
	echo '<iframe name="email_preview" src="admin_email_preview.php?id=';
	echo $email_id;
	echo '" width="100%" height="400" frameborder="1" scrolling="yes">';
	echo "\n</iframe>\n<br>\n";
	
	// Give them the mail button or the edit button
	echo "<center>\n<br>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "?action=mail&id=";
	echo $email_id;
	echo "'>Send Email</a>\n<br>\n</center>\n";
	echo "<center>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "'>Return to Emails</a>\n<br>\n</center>\n";
} else if ( $action == "preview_nosend" ) {
	echo "<center><h2>Pensacola Blues Email Portal</h2></center>\n";
	echo "<center><h2><br>Your Email</h2></center>\n";
	
	// Give them a preview of the email in a new page
	echo '<iframe name="email_preview" src="admin_email_preview.php?id=';
	echo $email_id;
	echo '" width="100%" height="400" frameborder="1" scrolling="yes">';
	echo "\n</iframe>\n<br>\n";
	
	// Give them only the back button
	echo "<center>\n<br>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "'>Return to Emails</a>\n<br>\n</center>\n";
} else if ( $action == "preview_first" ) {
	echo "<center><h2>Pensacola Blues Email Portal</h2></center>\n";
	echo "<center><h2><br>Your Email</h2></center>\n";
	
	// Go ahead and insert the email into the database
	$insert  = "INSERT INTO news_letters ( subject, body, htmlBody ) VALUES ( '";
	$insert .= $db->escape( $_POST["subject"] ) . "', '";
	$insert .= $db->escape( $_POST["body"] ) . "', '";
	$insert .= $db->escape( $_POST["htmlBody"] ) . "' )";
	$db->query( $insert );
	
	// Give them a preview of the email in a new page
	echo '<iframe name="email_preview" src="admin_email_preview.php?id=';
	echo $db->insert_id;
	echo '" width="100%" height="400" frameborder="1" scrolling="yes">';
	echo "\n</iframe>\n<br>\n";
	
	// Give them the mail button or the edit button
	echo "<center>\n<br>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "?action=mail&id=";
	echo $db->insert_id;
	echo "'>Send Email</a>\n<br>\n</center>\n";
	echo "<center>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "'>Return to Emails</a>\n<br>\n</center>\n";
} else {
	// Show all the past emails and allow them to add a new one
	$emails = $db->get_results( "SELECT * from news_letters ORDER by composeDate DESC" );
	echo "<center><h2><br><br>Pensacola Blues Email Portal</h2></center>\n";
	echo "<center><h2><br><br>Current Emails</h2></center>\n";
	echo '<table border="1" width="70%" align="center"><tr><td>';
	echo "\n";
	echo '<table border="0" cellpadding="7" align="center" width="95%">';
	echo "\n";
	
	// Print column headers
	echo "<tr>\n<td><strong>Date</strong></td>\n";
	echo "<td><strong>Subject</strong></td>\n";
	echo "<td><strong>Has Been Sent</strong></td>\n";
	echo "<td><strong>Sent Date</strong></td>\n";
	echo "<td><strong>Action</strong></td>\n";
	echo "</tr>\n";
	
	$first = 1;
	// Loop through all the emails and print them out in a table
	foreach ( $emails as $email )
	{
		echo "<tr>\n";
		if ( $email->hasBeenSent ) {
			echo '<td bgcolor="#DDDDDD">';
			echo $email->composeDate;
			echo "</td>\n";
			echo '<td bgcolor="#DDDDDD">';
			echo $email->subject;
			echo "</a></td>\n";
			echo '<td bgcolor="#DDDDDD">';
			echo "Yes";
			echo "</td>\n";
			echo '<td bgcolor="#DDDDDD">';
			echo $email->sentDate;
			echo "</td>\n";
			echo '<td bgcolor="#DDDDDD">';
			echo "<a href='";
			echo "?action=preview_nosend&id=";
			echo $email->id;
			echo "'>";
			echo "Preview</a></td>\n";
		} else {
			echo '<td>';
			echo $email->composeDate;
			echo "</td>\n";
			echo '<td>';
			echo $email->subject;
			echo "</a></td>\n";
			echo '<td>';
			echo "No";
			echo "</td>\n";
			echo '<td>';
			echo $email->sentDate;
			echo "</td>\n";
			echo '<td>';
			echo "<a href='";
			echo $_SERVER["PHP_SELF"];
			echo "?action=edit&id=";
			echo $email->id;
			echo "'>";
			echo "Edit</a> | <a href='";
			echo "?action=preview&id=";
			echo $email->id;
			echo "'>";
			echo "Preview</a></td>\n";
		}
		echo "</tr>\n";
	}
	echo '</table>';
	echo '</td></tr></table>',"\n";
	
	// Give them the add new email link
	echo "<center>\n<br>\n<a href='";
	echo $_SERVER["PHP_SELF"];
	echo "?action=add'>Add New Email</a>\n<br>\n</center>\n";
}
?>
<br><center><a href="<?php echo $site->home_admin_link; ?>">Main Page</a><br></center>
</body>
</html>
