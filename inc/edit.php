<?php

//Set checkbox values (if the checkbox is 'checked', it's value is 'on').
if ($_POST['append_toc'] == 'on')      $_POST['append_toc'] = 1;  
if ($_POST['include_in_toc'] == 'on')  $_POST['include_in_toc'] = 1;  

if ($_POST['update']) {
	$sql = ("UPDATE pages SET
		date_published = ".dbesc($_POST['date']).",
		parent_id = ".dbesc($_POST['parent_id']).",
		auth_level = ".dbesc($_POST['auth_level']).",
		title = ".dbesc($_POST['title']).",
		include_file = ".dbesc($_POST['include_file']).",
		style = ".dbesc($_POST['style']).",
		summary = ".dbesc($_POST['summary']).",
		append_toc = ".dbesc($_POST['append_toc']).",
		include_in_toc = ".dbesc($_POST['include_in_toc']).",
		body = ".dbesc($_POST['body'])."
		WHERE id = ".dbesc($_POST['edit_id']));
	$result = mysql_query($sql);
	$Page['body'] .= "<p>Page updated.</p>";
	$Page['body'] .= "<p>Return to <a href='".$_POST['edit_id']."'>
		".$_POST['title']."</a> (ID: ".$_POST['edit_id'].")</p>";
	// Do log entry.
	website_log("Page ".$_POST['edit_id']." edited.");

} else if ($_POST['insert']) {
	$sql = ("INSERT INTO pages SET
		date_published = ".dbesc($_POST['date']).",
		parent_id = ".dbesc($_POST['parent_id']).",
		auth_level = ".dbesc($_POST['auth_level']).",
		title = ".dbesc($_POST['title']).",
		include_file = ".dbesc($_POST['include_file']).",
		style = ".dbesc($_POST['style']).",
		summary = ".dbesc($_POST['summary']).",
		append_toc = ".dbesc($_POST['append_toc']).",
		include_in_toc = ".dbesc($_POST['include_in_toc']).",
		body = ".dbesc($_POST['body']));
	$result = mysql_query($sql);
	if (!$result)
	   $Page['error_message'] .= "<p>".mysql_error()."</p><pre>$sql</p>";
	$new_id = mysql_insert_id();
	$Page['body'] .= "<p>New page inserted.</p>";
	$Page['body'] .= "<p>Go to <a href='$new_id'>".$_POST['title']."</a> (ID: $new_id)</p>";
	// Do log entry.
	website_log("Page $new_id inserted.");
		
} else {
	if (!isset($_REQUEST['edit_id'])) {
		// Set defaults for new pages.
		$Editpage = array('include_in_toc'=>TRUE);
	} else {
		if ($_POST['preview']) {
			$Editpage = $_POST;
			$Editpage['id'] = $_POST['edit_id'];
		} else {
			$sql = "SELECT * FROM pages WHERE id=".dbesc($_REQUEST['edit_id']);
			$result = mysql_query($sql);
			if (mysql_num_rows($result)) {
				$Editpage = mysql_fetch_assoc($result);
			} else {
				$Page['error_message'] = "No page with ID ".$_REQUEST['edit_id']." exists.";
				$Editpage = array("parent_id"=>0);
			}
		}
	}
	
	if ($Editpage['append_toc']) { $toc_checked = "checked"; }
	if ($Editpage['include_in_toc']) { $include_in_toc_checked = "checked"; }
//********************************************************//
//********************************************************//
	//Get userlevels information, and build <select>
	$sql = "SELECT * FROM userlevels";
	$result = mysql_query($sql);
	$num_teams = mysql_num_rows($result);
	$team_select_element = ("<select id='auth_level' name='auth_level'>\n");
	for ($team=0; $team<$num_teams; $team++) {
		$row = mysql_fetch_assoc($result);
		if ($row['userlevel_id'] == $Editpage['auth_level']) {
			$selected = 'selected';
		} else {
			$selected = '';
		}
		$team_select_element .= "<option value='".$row['userlevel_id']."' $selected>".$row['userlevel_id']." - ".$row['userlevel_name']."</option>\n";
	}
	$team_select_element .= "</select>\n";
//********************************************************//
//********************************************************//
	//Get include files, and build <select>
	$inc_files = array(0=>"");
	if ($handle = opendir('inc/')) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." AND $file!="rte") {
				$inc_files[] = $file;
			}
		}
		closedir($handle);
	}
	sort($inc_files, SORT_STRING);
	$inc_select_element = "<select id='include_file' name='include_file'>\n";
	foreach($inc_files as $file) {
		if ($file == $Editpage['include_file']) {
			$selected = 'selected';
		} else {
			$selected = '';
		}
		$inc_select_element .= "<option value='$file' $selected>$file</option>\n";
	}
	$inc_select_element .= "</select>\n";
//********************************************************//
//********************************************************//
//Get parent page information, and build <select>
	$parent_select_element = "<select name='parent_id' style='width:100%'>";
	$sql = "SELECT id, append_toc FROM pages WHERE append_toc=1 OR id=945 OR id=1";
	$result = mysql_query($sql);
	$num_rows = mysql_num_rows($result);
	for ($row_num=0; $row_num<$num_rows; $row_num++) {
		$row = mysql_fetch_assoc($result);
		$parents_path[$row['id']] = get_breadcrumb($row['id'], 0);
	}
	asort ($parents_path);
	foreach ($parents_path as $parent_id => $parent_path) {
		if ($parent_id == $Editpage['parent_id']) $selected = 'selected';
		else $selected = '';
		$parent_select_element .= "<option value='$parent_id' $selected>$parent_path</option>";
	}
	$parent_select_element .= "</optgroup></select>\n";
//********************************************************//
//********************************************************//
	
$Page['style'] .= "textarea {width:100%}";
	
$Page['body'] .= ("
<form action='3' method='post'>
<input type='hidden' value='".$Editpage["id"]."' name='edit_id' />
<p>Category: $parent_select_element</p>
<p>
  Date (yyyy-mm-dd):<input type='text' name='date' size='10' value='".$Editpage["date_published"]."'>
  Authorisation Level: $team_select_element
  Include File: $inc_select_element
</p>
<p>Title: <input type='text' style='width:100%' name='title' size='40' value=\"".$Editpage["title"]."\"></p>
<p>Style: <textarea name=\"style\" rows=\"6\">".$Editpage["style"]."</textarea></p>
<p>Summary (inline tags only): <textarea name='summary' rows='6'>".$Editpage["summary"]."</textarea></p>
<p>Body (Full HTML): <textarea name='body' rows='10'>".htmlentities($Editpage["body"])."</textarea></p>
<p>
  Append TOC?<input type='checkbox' name='append_toc' $toc_checked />
  Include in TOC?<input type='checkbox' name='include_in_toc' $include_in_toc_checked />");
if (isset($Editpage['title'])) {
    $Page['body'] .= ("
        <input type='submit' name='update' value='Update'>
        <input type='submit' name='insert' 
            value='Insert As New'></p><p><strong>Insert As New</strong>
        doesn't change the record that you are now viewing.
    ");
} else {
    $Page['body'] .= "<input type='submit' name='insert' value='Insert New Page'>";
}
$Page['body'] .= "</p></form>";


}
?>