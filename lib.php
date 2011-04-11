<?php

function get_breadcrumb($page_id, $with_links = 1, $delim = " &raquo; ", $with_title = 1) {
	$sql = "SELECT * FROM pages WHERE id='$page_id'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
    $title = $row['title'];
    $PathTitleArray = array($row['title']);
    $PathIDArray = array($row['id']);
    $PathArray = array ();
    while ($row['id'] > 1) {
        $ParentID = $row['parent_id'];
        $sql = "SELECT * FROM pages WHERE id='".$row['parent_id']."'";
        $row = mysql_query($sql);
        if ($row) {
        	$row = mysql_fetch_assoc($row);
			$ParentTitle = $row['title'];
			array_push ($PathTitleArray, $ParentTitle);
			array_push ($PathIDArray, $ParentID);
		}
    }
    for ($i=0; $i < count($PathTitleArray); $i++) {
    	if ($with_links) {
	        array_unshift ($PathArray, "<a href='$PathIDArray[$i]'>$PathTitleArray[$i]</a>$delim");
		} else {
			array_unshift ($PathArray, $PathTitleArray[$i].$delim);
		}
    }
    array_pop($PathArray);
    $PathString = implode("", $PathArray);
    if ($with_title) {
		$PathString = $PathString.$title;
	}
    return $PathString;
}

function dbesc($var) {
	if (is_numeric($var)) return $var;
   	if (get_magic_quotes_gpc()) $var = stripslashes($var);
	return "'".mysql_real_escape_string($var)."'";
}

/**
 *
 * Password generation from Alan Prescott at
 * http://aspn.activestate.com/ASPN/Cookbook/PHP/Recipe/164739
 *
 * Modifications by Sam Wilson (2006):  revove captial O and 0.
 *
 */
function genpwd() {
    $length = 6;
    $vowels = 'aeiouyAEIUY';
    $consonants = 'bdghjlmnpqrstvwxzBDGHJLMNPQRSTVWXZ123456789@#$%^';
    $password = '';
    $alt = time() % 2;
    srand(time());
    for ($i=0; $i<$length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;   
}

function website_log($what) {
	global $User;
	mysql_query("INSERT INTO website_log SET what=".dbesc($what).", who='".$User['username']."'");
}

function wikiformat($in) {
		// Platform-independent newlines.
	$out = preg_replace("/(\r\n|\r)/", "\n", $in);
		// Paragraphs.
	$out = preg_replace('|(.*)|s', "<p>$1</p>", $out);
	$out = preg_replace('|\n+\s*\n+|', "</p>\n\n<p>", $out);
		// Remove paragraphs if they contain nothing (including only whitespace).
	$out = preg_replace('|<p>\s*</p>|', '', $out);
		// Remove nested paragraphs (some pages already have paragraphs marked up).
	$out = preg_replace('|<p><p>(.*)</p></p>|', '<p>$1</p>', $out);
		// Strong emphasis.
	$out = preg_replace("|'''(.*?)'''|s", "<strong>$1</strong>", $out);
		// Emphasis.
	$out = preg_replace("/''(.*?)''/", "<em>$1</em>", $out);
		// Links.
	$out = preg_replace("/\[\[([^|]*)\|([^\]]*)\]\]/", "<a href='$1'>$2</a>", $out);
	$out = preg_replace("/[^\"']http:\/\/([^\s]*)/", "<a href='http://$1'>$1</a>", $out);
		// Unordered lists.
	$out = preg_replace("|<p>\*|", "<ul>\n<li>", $out);
	$out = preg_replace("|\n\*|", "</li>\n<li>", $out);
	$out = preg_replace("|<li>(.*)</p>|", "<li>$1</li>\n</ul>", $out);
		// Ordered lists.
	$out = preg_replace("|<p>#|", "<ol>\n<li>", $out);
	$out = preg_replace("|\n#|", "</li>\n<li>", $out);
	$out = preg_replace("|<li>(.*)</p>|", "<li>$1</li>\n</ol>", $out);
	return $out;
}

function get_editperson_form($data) {
	global $Page;

	// Unless otherwise specified, post the results of the form back to the 
	// same page.
	if (!isset($data['form_action'])) {
		$data['form_action'] = $Page['id'];
	}
	
	if ($data['coordinator']) {
		$data['coordinator'] = 'checked';
	} else {
		$data['coordinator'] = '';
	}

	$out = "<form action='".$data['form_action']."' method='post'>
	<input type='hidden' name='person_id' value='".$data['person_id']."' />
	<p style='clear:both'>Co-ordinator?
	  <input type='checkbox' name='coordinator' ".$data['coordinator']." />
	</p>
	<div class='line'>
	  <div class='input' style='width:34%'>Username:
		<input type='text' name='username' value='".$data['username']."' />
	  </div>
	  <div class='input' style='width:33%'>New password:
		<input type='text' name='password' />
	  </div>
	  <div class='input' style='width:33%'>Level:
		".get_userlevel_select_element($data['userlevel'])."
	  </div>
	</div>
	<div class='line'>
	  <div class='input' style='width:50%'>First name:
		<input type='text' name='first_name' value='".$data['first_name']."' />
	  </div>
	  <div class='input' style='width:50%'>Surname:
		<input type='text' name='surname'  value='".$data['surname']."'/>
	  </div>
	</div>
	<div class='line'>
	  <div class='input' style='width:100%'>Email address:
		<input type='text' name='email_address' value='".$data['email_address']."' />
	  </div>
	</div>
	<div class='line'>
	  <div class='input' style='width:50%'>Main phone number:
		<input type='text' name='main_phone' value='".$data['main_phone']."' />
	  </div>
	  <div class='input' style='width:50%'>Other phone number:
		<input type='text' name='other_phone' value='".$data['other_phone']."' />
	  </div>
	</div>
	<p class='submit'><input type='submit' name='save' value='Save' /></p>
	</form>";
	return $out;
}

// Get userlevels information, and build <select>
function get_userlevel_select_element($sel) {
	$sql = "SELECT * FROM userlevels";
	$result = mysql_query($sql);
	$userlevelselect = "<select name='userlevel'>";
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['userlevel_id'] == $sel) {
			$selected = 'selected';
		} else {
			$selected = '';
		}
		$userlevelselect .= "<option value='".$row['userlevel_id']."' $selected>
			".$row['userlevel_id']." - ".$row['userlevel_name']."</option>";
	}
	$userlevelselect .= "</select>";
	return $userlevelselect;
}

?>
