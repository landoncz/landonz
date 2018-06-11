<?php
ini_set( "display_errors", "off" );

require_once( "lib/chart.php" );

$count_array = explode( "###", urldecode( $_GET["count"] ));
$days_array = explode( "###", urldecode( $_GET["days"] ));
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