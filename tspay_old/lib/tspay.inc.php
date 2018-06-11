<?php
// ----------------------------------------------------------------------
/* 
	Copyright 2006, Holley Grove Software Designs, All Rights Reserved
	Author : Holley Grove Software
	Date   : 04/2006
	Desc.  : File will include all necessary files & start the session.
*/
// ----------------------------------------------------------------------
ini_set( "display_errors", "off" );

session_start();

include_once( "utility.inc.php"        );
include_once( "ez_sql.php"             );
require_once( "class.smtp.php"         );
require_once( "class.phpmailer.php"    );
include_once( "site.inc.php"           );
include_once( "HtmlTemplate.class.php" );

// Variables here may need to change
$home_dir         = "http://www.onesourcepay.com";
$admin_dir        = "https://holleygrove.com/onesourcepay/admin";
$home_link        = "http://www.onesourcepay.com/index.php";
$home_admin_link  = "https://holleygrove.com/onesourcepay/admin/index.php";
$files_folder     = "files";
$documents_folder = "documents";
$secure_dir       = "https://holleygrove.com/onesourcepay/secure";

// Create the variable $site
$site = new site( $home_dir, $admin_dir, $home_link, $home_admin_link,
	$files_folder, $documents_folder, $secure_dir );
$site->update_hits();

?>
