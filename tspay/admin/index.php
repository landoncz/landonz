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

$title   = "Transaction Solutions Administration Portal";
$header  = file_get_contents( "html/main_header_admin.html" );
$content = file_get_contents( "html/index.html"       );
$footer  = file_get_contents( "html/main_footer.html" );
$script  = file_get_contents( "js/google_maps.js"     );
$script .= file_get_contents( "js/index.js"     );
$onload  = 'load()" onunload="GUnload()';

$count_array = array();
$days_array = array();

// Query for one month
$query  = "SELECT DAYOFMONTH(dateTime) AS my_day, COUNT(id) AS id_cnt FROM ";
$query .= "site_activity WHERE MONTH(dateTime)=MONTH(NOW()) AND ";
$query .= "YEAR(dateTime)=YEAR(NOW()) GROUP BY DAYOFMONTH(dateTime) ";
$query .= "ORDER BY DAYOFMONTH(dateTime) ASC LIMIT 31";
$data_set = $db->get_results( $query );
foreach( $data_set as $data ) {
	array_push( $days_array,  $data->my_day );
	array_push( $count_array, $data->id_cnt );
}
$days1 = urlencode( implode( "###", $days_array ));
$count1 = urlencode( implode( "###", $count_array ));

// Google maps stuff
$startdate = date( "Ymd", mktime(0, 0, 0, date("m")-1, date("d"),  date("Y")));
$query  = "SELECT ipAddress, DATE_FORMAT(dateTime, '%M %e, %Y %r') as my_date, ";
$query .= "latitude, longitude, city, country FROM site_activity WHERE latitude != ' ' AND ";
$query .= "longitude != ' ' GROUP BY ipAddress LIMIT 100";
$hits = $db->get_results( $query );

$js_lats = "";
$js_long = "";
$js_desc = "";
$my_count = 0;
foreach ( $hits as $hit )
{
	$js_lats .= "Lats[" . $my_count . "] = \"" . $hit->latitude . "\";\n";
	$js_long .= "Long[" . $my_count . "] = \"" . $hit->longitude . "\";\n";
	$js_desc .= "Desc[" . $my_count . "] = \"";
	$js_desc .= "IP Address: " . $hit->ipAddress . "<br>";
	$js_desc .= "City: " . trim($hit->city) . "<br>";
	$js_desc .= "Country: " . trim($hit->country) . "<br>";
	$js_desc .= "Visit Time: " . $hit->my_date . "\";\n";
	$my_count++;
}

// Run the sum statistics queries
$query  = "SELECT count(id) FROM site_activity WHERE week(DATE(dateTime))";
$query .= "=week(NOW()) AND YEAR(DATE(dateTime))=YEAR(NOW())";
$week_sum = $db->get_var( $query );

$query  = "SELECT count(id) FROM site_activity WHERE week(DATE(dateTime))";
$query .= "=week(NOW())-1 AND YEAR(DATE(dateTime))=YEAR(NOW())";
$lweek_sum = $db->get_var( $query );

$query  = "SELECT count(id) FROM site_activity WHERE month(DATE(dateTime))";
$query .= "=month(NOW()) AND YEAR(DATE(dateTime))=YEAR(NOW())";
$month_sum = $db->get_var( $query );

$query  = "SELECT count(id) FROM site_activity WHERE month(DATE(dateTime))";
$query .= "=month(NOW())-1 AND YEAR(DATE(dateTime))=YEAR(NOW())";
$lmonth_sum = $db->get_var( $query );

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",  $title     );
$page->SetParameter( "JAVASCRIPT",  $script    );
$page->SetParameter( "PAGE_HEADER", $header    );
$page->SetParameter( "PAGE_CONTENT",$content   );
$page->SetParameter( "ONLOAD_EVENT",$onload    );
$page->SetParameter( "DAYS1",       $days1   );
$page->SetParameter( "COUNT1",      $count1  );
$page->SetParameter( "MONTH_SUM",   $month_sum );
$page->SetParameter( "LMONTH_SUM",  $lmonth_sum);
$page->SetParameter( "WEEK_SUM",    $week_sum  );
$page->SetParameter( "LWEEK_SUM",   $lweek_sum  );
$page->SetParameter( "ARRAY_LENGTH",$my_count);
$page->SetParameter( "LATS_DECLARE",$js_lats );
$page->SetParameter( "LONG_DECLARE",$js_long );
$page->SetParameter( "DESC_DECLARE",$js_desc );
$page->SetParameter( "PAGE_FOOTER", $footer    );
$page->SetParameter( "MAIN_SITE",   $site->home_dir  );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
