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

$title   = "One Source Payments - Marinas";
$header  = file_get_contents( "html/header_marina.html" );
$content = file_get_contents( "html/marina.html"      );
$footer  = file_get_contents( "html/main_footer.html" );

// Javascript stuff for effects here

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title            );
$page->SetParameter( "JAVASCRIPT",  $script           );
$page->SetParameter( "PAGE_HEADER", $header           );
$page->SetParameter( "PAGE_CONTENT",$content          );
$page->SetParameter( "ONLOAD_EVENT",$onload           );
$page->SetParameter( "NAV_LINKS",   $nav_links        );
$page->SetParameter( "PAGE_FOOTER", $footer           );
$page->SetParameter( "MAIN_SITE",   $site->home_dir   );
$page->SetParameter( "SECURE_SITE", $site->secure_dir );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
