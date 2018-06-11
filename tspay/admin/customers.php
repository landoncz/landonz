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

$per_page = 15;
$title   = "Transaction Solutions Administration - Forms";
$header  = file_get_contents( "html/main_header_admin.html" );
$content = file_get_contents( "html/customers.html"         );
$footer  = file_get_contents( "html/main_footer.html"       );

if ( strlen(trim($_GET["action"])) > 0 ) $action = $db->escape(trim($_GET["action"]));
if ( strlen(trim($_POST["action"])) > 0 ) $action = $db->escape(trim($_POST["action"]));
if ( strlen(trim($_GET["email"])) > 0 ) $email = $db->escape(trim($_GET["email"]));
if ( strlen(trim($_POST["email"])) > 0 ) $email = $db->escape(trim($_POST["email"]));
if ( strlen(trim($_POST["firstName"])) > 0 ) $firstName = $db->escape(trim($_POST["firstName"]));
if ( strlen(trim($_POST["lastName"])) > 0 ) $lastName = $db->escape(trim($_POST["lastName"]));
if ( strlen(trim($_POST["street"])) > 0 ) $street = $db->escape(trim($_POST["street"]));
if ( strlen(trim($_POST["street2"])) > 0 ) $street2 = $db->escape(trim($_POST["street2"]));
if ( strlen(trim($_POST["city"])) > 0 ) $city = $db->escape(trim($_POST["city"]));
if ( strlen(trim($_POST["state"])) > 0 ) $state = $db->escape(trim($_POST["state"]));
if ( strlen(trim($_POST["zip"])) > 0 ) $zip = $db->escape(trim($_POST["zip"]));
if ( strlen(trim($_POST["phone"])) > 0 ) $phone = $db->escape(trim($_POST["phone"]));
if ( strlen(trim($_POST["category"])) > 0 ) $category = $db->escape(trim($_POST["category"]));
if ( strlen(trim($_POST["pageid"])) > 0 ) $next_page = $db->escape(trim($_POST["pageid"]));

if ( $action == "add" ) {
	// Show add form
	$content = file_get_contents( "html/customer_add_form.html" );
	
	$cat_q = "SELECT id, name FROM categories WHERE available";
	$cats = $db->get_results( $cat_q );
	$cat_html = "";
	foreach ( $cats as $cat )
	{
		$cat_html .= '<option value="' . $cat->id . '">' . $cat->name;
		$cat_html .= "</option>\n\t\t\t";
	}
} else if ( $action == "do_add" ) {
	// Process the user's arguements and add them to the database.
	if ( !empty( $email )) {
		$insert  = "INSERT INTO users ( email, category_id, firstName, lastName, ";
		$insert .= "street, street2, city, state, zip, phone ) VALUES ( '";
		$insert .= $email . "', '" . $category . "', '" . $firstName . "', '";
		$insert .= $lastName . "', '" . $street . "', '" . $street2 . "', '";
		$insert .= $city . "', '" . $state . "', '" . $zip . "', '";
		$insert .= $phone . "')";
		$db->query( $insert );
		//$db->debug();
	}
} else if ( $action == "edit" ) {
	// Show the edit form with user's current values
	if ( !empty( $email )) {
		$content = file_get_contents( "html/customer_edit_form.html" );
		
		// Get the user's details
		$user_q = "SELECT * FROM users WHERE email='" . $email . "' LIMIT 1";
		$user = $db->get_row( $user_q );
		
		// Get the category information for the option box
		$cat_q = "SELECT id, name FROM categories WHERE available";
		$cats = $db->get_results( $cat_q );
		$cat_html = "";
		foreach ( $cats as $cat )
		{
			$cat_html .= '<option value="' . $cat->id . '"';
			if ( $user->category_id == $cat->id ) $cat_html .= ' selected';
			$cat_html .= '>' . $cat->name;
			$cat_html .= "</option>\n\t\t\t";
		}
		
		// Set the values for the form with result from the database
		$firstName = $user->firstName;
		$lastName = $user->lastName;
		$street = $user->street;
		$street2 = $user->street2;
		$city = $user->city;
		$state = $user->state;
		$zip = $user->zip;
		$phone = $user->phone;
	}
} else if ( $action == "do_edit" ) {
	// User wants to edit a user
	if ( !empty( $email )) {
		// Update the database with the new information
		$query  = "UPDATE users SET firstName='" . $firstName . "', lastName='";
		$query .= $lastName . "', street='" . $street . "', street2='";
		$query .= $street2 . "', city='" . $city . "', state='";
		$query .= $state . "', zip='" . $zip . "', phone='" . $phone . "', category_id='";
		$query .= $category . "' WHERE email='" . $email . "' LIMIT 1";
		$db->query( $query );
		//$db->debug();
	}
} else if ( $action == "remove" ) {
	// User wants to edit a user
	if ( !empty( $email )) {
		// Update the database, turning the user off with the new information
		$query  = "UPDATE users SET available='0' WHERE email='" . $email;
		$query .= "' LIMIT 1";
		$db->query( $query );
	}
}

// Show the users
$user_html = "";
$is_odd = 0;
$start = $per_page * $next_page;
$total = $db->get_var( "SELECT COUNT( email ) FROM users" );

// Print out the pages table at the top first
$page_html  = '<table align="center" border="0">';
$page_html .= '<tr><td align="center">Page ' . ($next_page + 1) . ' Customers ';
$page_html .= $start . " - " . ($start + $per_page - 1) . "<br>\n";
for ( $i=0;$i<$total;$i=$i+$per_page) {
	if ( $i == ($next_page * $per_page) ) {
		$page_html .= (($i / $per_page) + 1) . "\n";
	} else {
		$page_html .= '<a href="' . $_SERVER["PHP_SELF"] . '?pageid=' . ($i / $per_page);
		$page_html .= '">' . (($i / $per_page) + 1) . "</a>\n";
	}
}
$page_html .= "</td>\n</tr>\n</table>\n";

$query = "SELECT users.email, users.firstName, users.lastName, ";
$query .= "categories.name FROM users LEFT JOIN categories ON ";
$query .= "users.category_id=categories.id WHERE users.available ";
$query .= "ORDER BY category_id, users.lastName LIMIT " . $start . ", " . $per_page;
$users = $db->get_results( $query );
foreach ( $users as $user )
{
	// Give the row a background color
	if ( $is_odd ) {
		$is_odd = 0;
		$user_html .= "<tr>\n\t\t\t<td align='left' bgcolor='#EBEBEB'><a href='";
		$user_html .= $_SERVER["PHP_SELF"] . "?action=edit&email=";
		$user_html .= urlencode( $user->email ) . "' title='Edit User'>";
		$user_html .= $user->lastName . ", " . $user->firstName . "</a></td>\n\t\t\t";
		$user_html .= "<td align='left' class='normalText' bgcolor='#EBEBEB'>";
		$user_html .= $user->name . "</td>\n\t\t\t<td align='left' class='normalText' bgcolor='#EBEBEB'>";
		$user_html .= "<a href='mailto:" . $user->email . "'>" . $user->email;
		$user_html .= "</a></td>\n\t\t\t";
		$user_html .= "<td align='left' class='normalText' bgcolor='#EBEBEB'>";
		$user_html .= "<a href='" . $_SERVER["PHP_SELF"];
		$user_html .= "?action=remove&email=" . $user->email;
		$user_html .= "'>Delete</a></td></tr>\n";
	} else {
		$is_odd = 1;
		$user_html .= "<tr>\n\t\t\t<td align='left'><a href='";
		$user_html .= $_SERVER["PHP_SELF"] . "?action=edit&email=";
		$user_html .= urlencode( $user->email ) . "' title='Edit User'>";
		$user_html .= $user->lastName . ", " . $user->firstName . "</a></td>\n\t\t\t";
		$user_html .= "<td align='left' class='normalText'>";
		$user_html .= $user->name . "</td>\n\t\t\t<td align='left' class='normalText'>";
		$user_html .= "<a href='mailto:" . $user->email . "'>" . $user->email;
		$user_html .= "</a></td>\n\t\t\t";
		$user_html .= "<td align='left' class='normalText'>";
		$user_html .= "<a href='" . $_SERVER["PHP_SELF"];
		$user_html .= "?action=remove&email=" . $user->email;
		$user_html .= "'>Delete</a></td></tr>\n";
	}
}

// Set all the content of the page to the page variables
$page->SetParameter( "PAGE_TITLE",    $title     );
$page->SetParameter( "JAVASCRIPT",    $script    );
$page->SetParameter( "PAGE_HEADER",   $header    );
$page->SetParameter( "PAGE_CONTENT",  $content   );
$page->SetParameter( "ONLOAD_EVENT",  $onload    );
$page->SetParameter( "EMAIL",         $email     );
$page->SetParameter( "CATEGORY_OPTIONS", $cat_html );
$page->SetParameter( "FIRST_NAME",    $firstName );
$page->SetParameter( "LAST_NAME",     $lastName  );
$page->SetParameter( "STREET",        $street    );
$page->SetParameter( "STREET2",       $street2   );
$page->SetParameter( "CITY",          $city      );
$page->SetParameter( "STATE",         $state     );
$page->SetParameter( "ZIP",           $zip       );
$page->SetParameter( "PHONE",         $phone     );
$page->SetParameter( "PAGE_HTML",     $page_html );
$page->SetParameter( "CUSTOMER_CONTENTS", $user_html );
$page->SetParameter( "PAGE_FOOTER",   $footer    );
$page->SetParameter( "MAIN_SITE",     $site->home_dir  );

// Now send the completed instance of the class to the browser
$page->CreatePage();

?>
