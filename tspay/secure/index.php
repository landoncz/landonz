<?php
// ---------------------------------------------------------------
/* Following section should be included on every page */
// Copyright 2007 Holley Grove Software Designs, All Rights Reserved
// Include files
require( "lib/auth_user.php" );

// Create the page variable to handle HTML generation and printing
$page = new HtmlTemplate( "html/main_template_onload.html" );
// ---------------------------------------------------------------
//

$title   = "Transaction Solutions, LLC";
$onload  = "";
$header  = file_get_contents( "html/main_header.html" );
$content = file_get_contents( "html/index.html"       );
$footer  = file_get_contents( "html/main_footer.html" );

// Loop through the forms and show them to the user
$query  = "SELECT * ";
$query .= "FROM forms WHERE available";
$forms  = $db->get_results( $query );
$form_html = "";
foreach( $forms as $form )
{
	// Get the category name
	$query = "SELECT name FROM categories WHERE id='" . $form->category_id . "' LIMIT 1";
	$cat = $db->get_var( $query );
	
	$form_html .= "<tr>\n\t\t\t\t<td class='normalText'>" . $cat . "</td>\n\t\t\t\t";
	$form_html .= "<td class='normalText' align='left'>" . $form->description . "</td>\n\t\t\t\t";
	$form_html .= "<td class='normalText' align='left'><a href='" . $form->fileName;
	$form_html .= "' class='normalLink' align='left'>" . $form->fileName . "</a></td>\n\t\t\t\t";
	$form_html .= "</tr>\n\t\t\t";
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title            );
$page->SetParameter( "JAVASCRIPT",  $script           );
$page->SetParameter( "PAGE_HEADER", $header           );
$page->SetParameter( "PAGE_CONTENT",$content          );
$page->SetParameter( "ONLOAD_EVENT",$onload           );
$page->SetParameter( "NAV_LINKS",   $nav_links        );
$page->SetParameter( "FORM_HTML",   $form_html        );
$page->SetParameter( "PAGE_FOOTER", $footer           );
$page->SetParameter( "MAIN_SITE",   $site->home_dir   );
$page->SetParameter( "SECURE_SITE", $site->secure_dir );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
