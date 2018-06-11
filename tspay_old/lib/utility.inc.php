<?php
// ----------------------------------------------------------------------------
/*
-	Author : Holley Grove Software - Landon Zabcik
-	Date   : 04/2006
-	Desc.  : File contains various site utility functions
-	Note   : Where functions were taken from outside sources, if copyrights or
-				licenses were provided, they are kept in tact in this file.
*/
// ----------------------------------------------------------------------------

if (!function_exists("str_split")) {
	function str_split($string, $length = 1) {
		if ($length <= 0) {
			trigger_error(__FUNCTION__."(): The the length of each segment must be greater then zero:", E_USER_WARNING);
			return false;
		}
		$splitted  = array();
		while (strlen($string) > 0) {
			$splitted[] = substr($string, 0, $length);
			$string = substr($string, $length);
		}
		return $splitted;
	}
}

function in_array_nocase( $search, &$array )
{
	$search = strtolower($search);
	foreach ($array as $item) {
		if (strtolower($item) == $search) return 1;
	}
	return 0;
}

?>