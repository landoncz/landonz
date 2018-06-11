<?php
require_once( "../lib/tspay.inc.php" );
require_once( "lib/auth_user.php" );
require_once( "lib/chart.php" );

$count_array = array();
$days_array = array();

// Query for last 100 days
$startdate = date( "Ymd", mktime(0, 0, 0, date("m"), date("d")-100,  date("Y")));
$query  = "SELECT DATE_FORMAT(dateTime, '%Y-%m-%d') AS my_day, COUNT(id) AS id_cnt FROM ";
$query .= "site_activity WHERE dateTime > '" . $startdate . "' GROUP BY DAYOFYEAR(dateTime) ORDER BY dateTime ASC";
$data_set = $db->get_results( $query, ARRAY_N );
$counter = 0;
for ( $i=100;$i>0;$i-- )
{
	$myDate = date( "Y-m-d", mktime(0, 0, 0, date("m"), date("d")-$i,  date("Y")));
	array_push( $days_array,  101 - $i );
	if ( $myDate == $data_set[$counter][0] ) {
		// The date has hits for it, insert the number of hits
		array_push( $count_array, $data_set[$counter][1] );
		$counter++;
	} else {
		// The date does not have any hits, insert 0
		array_push( $count_array, 0 );
	}
}

$chart = new chart(580, 200);
$chart->plot( $count_array, $days_array, "#CECECE", "gradient", "#670000", 1 );
if ( $days_array[sizeof($days_array)-1] > 32 ) {
	$chart->set_labels( "Time", "Visitors" );
} else {
	$chart->set_labels( "Day", "Visitors" );
}
$chart->set_margins( 50, 5, 5, 40 );
$chart->set_grid_color( "#D1D1D1" );
$chart->stroke();

?>