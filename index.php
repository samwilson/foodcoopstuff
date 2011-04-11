<?php

session_start();
include 'config.php';
include 'lib.php';

//----------------------------------------------------------------------------//
//------------------ Get this page, or else send 404error ------------------//
//----------------------------------------------------------------------------//
if (isset($_REQUEST['id'])) $id = $_REQUEST['id']; else $id = 1;
$sql = "SELECT * FROM pages, userlevels WHERE pages.id=".dbesc($id)."
                                          AND userlevels.userlevel_id=pages.auth_level
                                          LIMIT 1";
$result = mysql_query($sql);
$Page = mysql_fetch_assoc($result);
if (mysql_num_rows($result) == 0) {
	header('HTTP/1.0 404 Not Found');
	$Page = array(
		'title' => 'Error 404: Page Not Found',
		'auth_level' => 0,
		'error_message' => "The page that you have requested does not exist.");
}

//----------------------------------------------------------------------------//
//-------------- check that the current user can view this page --------------//
//----------------------------------------------------------------------------//
$User = array('userlevel'=>0);
if ($_SESSION['logged-in']) {
	$current_session_id = dbesc(session_id());
	$current_ip = dbesc($_SERVER['REMOTE_ADDR']);
	$sql = ("SELECT * FROM people
		WHERE username = '".$_SESSION['username']."'
		AND current_session_id = $current_session_id
		AND current_ip = $current_ip LIMIT 1");
	$result = mysql_query($sql);
	if (mysql_error()) $Page['error_message'] .= mysql_error();
	$num = mysql_num_rows($result);
	if ($num) {
		$User = mysql_fetch_assoc($result);
		$User['logged-in'] = TRUE;
	} else {
		$User['logged-in'] = FALSE;
		$_SESSION['logged-in'] = FALSE;
		$Page['error_message'] .= "<p>Your session has expired.  Please log in again.</p>";
	}
}

if ($Page['auth_level'] != 0 && $User['userlevel'] < $Page['auth_level'] && $User['logged-in']) {
	$Page = array();
	$Page['parent_id'] = '1';
	$Page['title'] = "Access Denied";
	$Page['error_message'] = "You are not authorised to view this page.";
}

if ($Page['auth_level'] != 0 && !$User['logged-in']) {
	$Page = array();
	$Page['parent_id'] = '1';
	$Page['title'] = "Please Login";
	$Page['error_message'] = "You have tried to view a protected page but
		you are not logged in.  Please log in and try again.";
	$Page['needs_login'] = true;
	$Page['auth_level'] = '0';
}

//----------------------------------------------------------------------------//
// Include a file if neccessary.
//----------------------------------------------------------------------------//
if ($Page['include_file'] != '') {
	$include_file = "inc/".$Page['include_file'];
	if (file_exists($include_file)) {
		require_once($include_file);
	} else {
		$Page['error_message'] = "<em>".$include_file."</em> does not exist.";
	}
}

//----------------------------------------------------------------------------//
// Get breadcrumb
//----------------------------------------------------------------------------//
if ( $Page['id'] ) {
	$Page['urhere'] = get_breadcrumb( $Page['id'] );
}

//----------------------------------------------------------------------------//
// Format page body, taking into account HTML and Mediawiki syntax, but not if
// an include file has been used (gives greater flexibility for plugins).
//----------------------------------------------------------------------------//
if (!$Page['include_file']) {
	$Page['formatted_body'] = wikiformat($Page['body']);
}

//----------------------------------------------------------------------------//
// Table of contents
//----------------------------------------------------------------------------//
if ($Page['append_toc']) {
    $sql = "SELECT * FROM pages WHERE parent_id = ".dbesc($Page['id']).
                              " AND auth_level <= ".dbesc($User['userlevel']).
                              " AND include_in_toc = 1".
                              " ORDER BY date_published DESC, title";
    $result = mysql_query($sql);
	// if this node has any children, only then do we build the TOC.
	if (mysql_num_rows($result) > 0) {
		$Page['toc'] .= "<ul>";
		while ($toc = mysql_fetch_assoc($result)) {
			$Page['toc'].= "<li><a href='".$toc['id']."'>".$toc['title'];
			if ($toc['date_published'] !== "0000-00-00") {
				$Page['toc'] .= " (".$toc['date_published'].")";
			}
			$Page['toc'] .= "</a></li>";
		}
		$Page['toc'] .= "</ul>";
	}
}

//----------------------------------------------------------------------------//
// Build list of this page's siblings (for the sidebar).
//----------------------------------------------------------------------------//
$sql = "SELECT id, title, date_published FROM pages WHERE parent_id = ".dbesc($Page['parent_id']).
                            " AND auth_level <= ".dbesc($User['userlevel']).
                            " AND include_in_toc = 1".
                            " ORDER BY date_published DESC, title";
$result = mysql_query($sql);
if (mysql_num_rows($result) > 0) {
	$Page['siblings'] = "<ul id='siblings'>";
	while ($sibling = mysql_fetch_assoc($result)) {
		if ($sibling['id']==$Page['id']) $class = "class='current' ";
		else $class = '';
		if ($sibling['date_published'] !== "0000-00-00") $date = " (".$sibling['date_published'].")";
		$Page['siblings'] .= "<li><a href='".$sibling['id']."' $class>".$sibling['title']."$date</a></li>";
	}
	$Page['siblings'] .= "</ul>";
}

//----------------------------------------------------------------------------//
//--------Output the HTML page------------
//----------------------------------------------------------------------------//
require_once('template.php');


?>