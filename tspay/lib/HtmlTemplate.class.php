<?php

/*	File Name          : html.inc.php
	Called From        : all
	Directory          : class
	Author             : Landon Zabcik
	Files used         : N/A, this file is used by all php pages that
				generate html, but it uses no files itself.
	Date Last Modified : 02/02/2007

	Description:  This class reads in an html template, sets the values,
		and sends it to the browser.  This was done as a class with
		templates to aid in changes made to the site.  Because the
		code is contained in separate template files, a uniform look
		can be given to the site, and things such as background color,
		html style, etc. can be changed to the entire site by only
		changing the template files.  This class creates the pages
		and sends the pages to the variables, replacing all of the
		<!--{}--> tags in the template files with the values passed
		to the functions below.

*/

class HtmlTemplate {

	// Set the attributes.
	var $template;
	var $html;
	var $parameters = array();
	var $message;

	// This function sets the template that will be used
	function HtmlTemplate ( $template ) {
		$this->template = $template;
		
		// Read the template into an array then create a string
		$this->html = implode( "",( file( $this->template )));
		
		// First check for error messages along the URL
		if ( !empty( $_GET["error_message"] )) {
			$this->SetErrorMessage( urldecode( $_GET["error_message"] ));
		} else if ( !empty( $_GET["good_message"] )) {
			$this->SetGoodMessage( urldecode( $_GET["good_message"] ));
		}
	}

	// This function will be allowed to set the parameters
	function SetParameter( $variable, $value ) {
		$this->parameters[$variable] = $value;
	}

	// This function creates the page
	function CreatePage() {		
		// Set an error message if any
		$this->SetParameter( "HG_MESSAGE", $this->message );
		
		// Loop through all the paramters and set the variables to the values
		foreach ( $this->parameters as $key => $value ) {
			$template_name = '<!--{' . $key . '}-->';
			$this->html = str_replace( $template_name, $value, $this->html );
		}
		
		// Flush the html to the browswer
		echo $this->html;
	}
	
	function SetErrorMessage( $the_message )
	{
		$this->message =  '<div><h2 align="center" style="color: #EF0000;">';
		$this->message .= "The following problem(s) occurred: ";
		$this->message .= "<br>" . $the_message . "</h2>";
		$this->message .= "</div><br>\n";
	}
	
	function SetGoodMessage( $the_message )
	{
		$this->message =  '<div><h2 align="center">';
		$this->message .= $the_message . "</h2>";
		$this->message .= "</div><br>\n";
	}
}  // End of class

?>