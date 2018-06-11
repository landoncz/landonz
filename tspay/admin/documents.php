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

$title   = "Transaction Solutions Administration - Forms";
$header  = file_get_contents( "html/main_header_admin.html" );
$content = file_get_contents( "html/documents.html"         );
$footer  = file_get_contents( "html/main_footer.html"       );

if ( strlen(trim($_GET["action"])) > 0 ) $action = $db->escape(trim($_GET["action"]));
if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_GET["doc_id"])) > 0 ) $doc_id = $db->escape(trim($_GET["doc_id"]));
if ( strlen(trim($_POST["doc_id"])) > 0 ) $doc_id = $db->escape(trim($_POST["doc_id"]));
if ( strlen(trim($_POST["doc_title"])) > 0 ) $doc_title = $db->escape(trim($_POST["doc_title"]));
if ( strlen(trim($_POST["category"])) > 0 ) $category = $db->escape(trim($_POST["category"]));
if ( strlen(trim($_POST["uploadedfile"])) > 0 ) $uploadedfile = $db->escape(trim($_POST["uploadedfile"]));

if ( $action == "upload" ) {
	// Show upload form
	$content = file_get_contents( "html/doc_upload_form.html" );
	
	$cat_q = "SELECT id, name FROM categories WHERE available";
	$cats = $db->get_results( $cat_q );
	$cat_html = "";
	foreach ( $cats as $cat )
	{
		$cat_html .= '<option value="' . $cat->id . '">' . $cat->name;
		$cat_html .= "</option>\n\t\t\t";
	}
} else if ( $action == "do_upload" ) {
	// Move the uploaded file into the files directory
	$base_path   = "../" . $site->documents_folder . "/";
	$target_path = $base_path . basename( str_replace( ' ', '', $_FILES['uploadedfile']['name']));
	$extension   = "." . array_pop( explode( '.', basename( $_FILES['uploadedfile']['name'])));
	$bare_name   = basename( $_FILES['uploadedfile']['name'], $extension );
	
	chown( $_FILES['uploadedfile']['tmp_name'], "blues" );
	chmod( $_FILES['uploadedfile']['tmp_name'], 0666 );
	if ( !move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo "<li>";
		echo "There was an error uploading the document ";
		echo $_FILES['uploadedfile']['name'];
		echo ", please try again or contact the system administrator.<br>\n";
		echo "</li></ul>";
		echo "</h4>\n";
		die;
	}
	
	chown( $target_path, "blues" );
	chmod( $target_path, 0666 );
	
	// Perform the insert into the database
	if ( empty( $doc_title )) $doc_title = $bare_name . $extension;
	$insert = "INSERT INTO forms ( fileName, description, category_id ) VALUES ( '";
	$insert .= $target_path . "', '" . $doc_title . "', '" . $category . "' )";
	$db->query( $insert );
} else if ( $action == "remove" ) {
	// Set the file as unavailable (removed)
	$query = "UPDATE forms SET available=0 WHERE id='" . $doc_id . "' LIMIT 1";
	$db->query( $query );
	$query = "SELECT fileName FROM forms WHERE id='" . $doc_id . "' LIMIT 1";
	$docName = $db->get_var( $query );
	$new_name  = "../" . $site->documents_folder . "/";
	$new_name .= str_replace( "../" . $site->documents_folder, "trash", $docName );
	rename( $docName, $new_name );
}

// Show the files
$file_html = "";
$is_odd = 0;
$query = "SELECT forms.id, forms.fileName, forms.description, forms.dateTime, ";
$query .= "categories.name FROM forms LEFT JOIN categories ON ";
$query .= "forms.category_id=categories.id WHERE forms.available";
$files = $db->get_results( $query );
foreach ( $files as $file )
{
	// Give the row a background color
	if ( $is_odd ) {
		$is_odd = 0;
		$file_html .= "<tr>\n\t\t\t<td align='left' bgcolor='#EBEBEB'><a href='" . $file->fileName;
		$file_html .= "' title='Download File'>" . $file->description . "</a></td>\n\t\t\t";
		$file_html .= "<td align='left' class='normalText' bgcolor='#EBEBEB'>";
		$file_html .= $file->name . "</td>\n\t\t\t";
		$file_html .= "<td align='left' class='normalText' bgcolor='#EBEBEB'>" . $file->dateTime;
		$file_html .= "</td>\n\t\t\t<td bgcolor='#EBEBEB'><a href='" . $_SERVER["PHP_SELF"];
		$file_html .= "?action=remove&doc_id=" . $file->id;
		$file_html .= "'>Delete</a></td></tr>\n";
	} else {
		$is_odd = 1;
		$file_html .= "<tr>\n\t\t\t<td align='left'><a href='" . $file->fileName;
		$file_html .= "' title='Download File'>" . $file->description . "</a></td>\n\t\t\t";
		$file_html .= "<td align='left' class='normalText'>";
		$file_html .= $file->name . "</td>\n\t\t\t";
		$file_html .= "<td align='left' class='normalText'>" . $file->dateTime;
		$file_html .= "</td>\n\t\t\t<td><a href='" . $_SERVER["PHP_SELF"];
		$file_html .= "?action=remove&doc_id=" . $file->id;
		$file_html .= "'>Delete</a></td></tr>\n";	
	}
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",    $title     );
$page->SetParameter( "JAVASCRIPT",    $script    );
$page->SetParameter( "PAGE_HEADER",   $header    );
$page->SetParameter( "PAGE_CONTENT",  $content   );
$page->SetParameter( "ONLOAD_EVENT",  $onload    );
$page->SetParameter( "CATEGORY_OPTIONS", $cat_html );
$page->SetParameter( "FILE_CONTENTS", $file_html );
$page->SetParameter( "PAGE_FOOTER",   $footer    );
$page->SetParameter( "MAIN_SITE",     $site->home_dir  );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
