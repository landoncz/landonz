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

$title   = "Transaction Solutions Administration - File Manager";
$header  = file_get_contents( "html/main_header_admin.html" );
$content = file_get_contents( "html/file_manager.html"      );
$footer  = file_get_contents( "html/main_footer.html"       );

if ( strlen(trim($_GET["action"])) > 0 ) $action = $db->escape(trim($_GET["action"]));
if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_GET["file_id"])) > 0 ) $file_id = $db->escape(trim($_GET["file_id"]));
if ( strlen(trim($_POST["file_id"])) > 0 ) $file_id = $db->escape(trim($_POST["file_id"]));
if ( strlen(trim($_POST["file_title"])) > 0 ) $file_title = $db->escape(trim($_POST["file_title"]));
if ( strlen(trim($_POST["uploadedfile"])) > 0 ) $uploadedfile = $db->escape(trim($_POST["uploadedfile"]));

if ( $action == "upload" ) {
	// Show upload form
	$content = file_get_contents( "html/file_upload_form.html" );
} else if ( $action == "do_upload" ) {
	// Move the uploaded file into the files directory
	$base_path   = $site->files_folder . "/";
	$target_path = $base_path . basename( str_replace( ' ', '', $_FILES['uploadedfile']['name']));
	$extension   = "." . array_pop( explode( '.', basename( $_FILES['uploadedfile']['name'])));
	$bare_name   = basename( $_FILES['uploadedfile']['name'], $extension );
	
	chown( $_FILES['uploadedfile']['tmp_name'], "blues" );
	chmod( $_FILES['uploadedfile']['tmp_name'], 0666 );
	if ( !move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo "<li>";
		echo "There was an error uploading the file ";
		echo $_FILES['uploadedfile']['name'];
		echo ", please try again or contact the system administrator.<br>\n";
		echo "</li></ul>";
		echo "</h4>\n";
		die;
	}
	
	chown( $target_path, "blues" );
	chmod( $target_path, 0666 );
	
	// Perform the insert into the database
	if ( empty( $file_title )) $file_title = $bare_name . $extension;
	$insert = "INSERT INTO files ( fileName, description ) VALUES ( '";
	$insert .= $target_path . "', '" . $file_title . "' )";
	$db->query( $insert );
} else if ( $action == "remove" ) {
	// Set the file as unavailable (removed)
	$query = "UPDATE files SET available=0 WHERE id='" . $file_id . "' LIMIT 1";
	$db->query( $query );
	$query = "SELECT fileName FROM files WHERE id='" . $file_id . "' LIMIT 1";
	$fileName = $db->get_var( $query );
	$new_name  = $site->files_folder . "/";
	$new_name .= str_replace( $site->files_folder, "trash", $fileName );
	rename( $fileName, $new_name );
}

// Show the files
$file_html = "";
$is_odd = 0;
$query = "SELECT * FROM files WHERE available";
$files = $db->get_results( $query );
foreach ( $files as $file )
{
	// Give the row a background color
	if ( $is_odd ) {
		$is_odd = 0;
		$file_html .= "<tr>\n\t\t\t<td align='left' bgcolor='#EBEBEB'><a href='" . $file->fileName;
		$file_html .= "' title='Download File'>" . $file->description . "</a></td>\n\t\t\t";
		$file_html .= "<td align='left' class='normalText' bgcolor='#EBEBEB'>" . $file->dateTime;
		$file_html .= "</td>\n\t\t\t<td bgcolor='#EBEBEB'><a href='" . $_SERVER["PHP_SELF"];
		$file_html .= "?action=remove&file_id=" . $file->id;
		$file_html .= "'>Delete</a></td></tr>\n";
	} else {
		$is_odd = 1;
		$file_html .= "<tr>\n\t\t\t<td align='left'><a href='" . $file->fileName;
		$file_html .= "' title='Download File'>" . $file->description . "</a></td>\n\t\t\t";
		$file_html .= "<td align='left' class='normalText'>" . $file->dateTime;
		$file_html .= "</td>\n\t\t\t<td><a href='" . $_SERVER["PHP_SELF"];
		$file_html .= "?action=remove&file_id=" . $file->id;
		$file_html .= "'>Delete</a></td></tr>\n";	
	}
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",    $title     );
$page->SetParameter( "JAVASCRIPT",    $script    );
$page->SetParameter( "PAGE_HEADER",   $header    );
$page->SetParameter( "PAGE_CONTENT",  $content   );
$page->SetParameter( "ONLOAD_EVENT",  $onload    );
$page->SetParameter( "FILE_CONTENTS", $file_html );
$page->SetParameter( "PAGE_FOOTER",   $footer    );
$page->SetParameter( "MAIN_SITE",     $site->home_dir  );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
