<?php 

$Page['style'] .= "table {width: 100%}
td {vertical-align:top}
table select {display:block; margin:0; width:100%}
td a {text-decoration:none}
.monthlabel {text-align:right; color:red; margin:0; padding:0}
.shiftlist {margin:0; padding:0}
";

$Page['script'] .= "function init() {
    window.location='#today';
}";        
        
if ($_GET['action']=='edit') {
    $shift = $_GET['shift'].'_shift';
    $rosterinfo = mysql_fetch_assoc(mysql_query("SELECT $shift, date, DATE_FORMAT(date, '%W, %M %D') AS formatted_date FROM `co-ordinators_roster`
        WHERE date=".dbesc($_GET['date'])));
    $sql = "SELECT * FROM people WHERE id = ".dbesc($rosterinfo[$shift])."";
    $from_person = mysql_fetch_assoc(mysql_query($sql));
    $Page['body'] .= "<form action='6' method='post' style='width:50%; margin:auto' />
        <input type='hidden' name='date' value='".$rosterinfo['date']."' />
        <input type='hidden' name='shift' value='$shift' />
        <p>You are changing the ".ucwords($_GET['shift'])."
        shift of ".$rosterinfo['formatted_date']." from <br />
        ".$from_person['first_name']." ".$from_person['surname']." to 
        <select name='new_person'>".get_coordie_options()."</select> (leave blank to drop shift).</p>
        <p style='text-align:center'><input type='submit' name='saveedit' value='Save' />
        <a href='6'>[Cancel]</a></p></form>";
} else {

    if ($_POST['saveedit']) {
        $sql = "UPDATE `co-ordinators_roster`
            SET `".$_POST['shift']."` = ".dbesc($_POST['new_person'])."
            WHERE date = ".dbesc($_POST['date']);
        mysql_query($sql);
        $Page['body'] .= "<p>Change saved.</p>";
    }
    
    // Only save the used shifts.
    if ($_POST['save']) {
		save_shifts('morningA');
		save_shifts('morningB');
		save_shifts('afternoonA');
		save_shifts('afternoonB');
		save_shifts('eveningA');
		save_shifts('eveningB');
    }
    
    if ($_GET['m']) $current_month = $_GET['m']; else $current_month = date('n');
    if ($_GET['y']) $current_year = $_GET['y']; else $current_year = date('Y');
    
    require_once 'Calendar/Month/Weekdays.php';
    require_once 'Calendar/Decorator/Textual.php';
    require_once 'Calendar/Decorator/Weekday.php';
    $Month = new Calendar_Month_Weekdays($current_year, $current_month, 1);
    $Month->build();
    
    $textual_month = & new Calendar_Decorator_Textual($Month);

    if ($Month->thisMonth()==1) $prev_qs = "y=".($current_year-1)."&m=12";
    else $prev_qs = "y=$current_year&m=".($current_month-1);
    if ($Month->thisMonth()==12) $next_qs = "y=".($current_year+1)."&m=1";
    else $next_qs = "y=$current_year&m=".($current_month+1);
    
    $Page['body'] .= "
        <div style='width:40em; margin:auto; text-align:justify'>
        <p>You can use [edit] to swap or drop a shift until the end of the week
        (Sunday 5PM to be precise), at which time all shifts are confirmed
        and points allocated.  <strong>You cannot swap a shift after this time.</strong>
        Any shifts left empty can still be edited and, if they are, will be part of the
        following Sunday's points allocation.</p>
        </div>
        <form action='6' method='post'>
        <p class='submit'><input type='submit' value='Save changes' name='save' /></p>
        <table border='1'>
        <tr>
          <th style='padding:1em; border-right:0'>
            <a href='6?$prev_qs'>&laquo; ".$textual_month->prevMonthName()."</a>
          </th>
          <th colspan='5' style='padding:1em; border-left: 0; border-right:0'>
            ".$textual_month->thisMonthName()." $current_year
          </th>
          <th style='padding:1em; border-left:0'>
            <a href='6?$next_qs'>".$textual_month->nextMonthName()." &raquo;</a>
          </th>
        </tr>
        <tr>";
    $weekdays = $textual_month->orderedWeekdays();
    foreach ($weekdays as $weekday) {
        $Page['body'] .= "<th>$weekday</th>";
    }
    $Page['body'] .= "</tr>";
    
    // Main calendar table loop.
    while ($Day = $Month->fetch()) {
        if ($Day->isFirst()) {
            $Page['body'] .= "<tr>";
        }
    
        $textual_month = & new Calendar_Decorator_Textual($Day);
        $textual_day = & new Calendar_Decorator_Weekday($Day);
        $thisdate = $Day->thisYear()."-".$Day->thisMonth()."-".$Day->thisDay();
        
        // Get shift info, or set default.
        $rosterinfo = array('date'=>$thisdate); // Default, if day has no shifts
        $sql = "SELECT * FROM `co-ordinators_roster` WHERE `date`='$thisdate'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $rosterinfo = mysql_fetch_assoc($res);
    	}
		$morningAshift   = get_shift_select($rosterinfo, 'morningA');
		$morningBshift   = get_shift_select($rosterinfo, 'morningB');
		$afternoonAshift = get_shift_select($rosterinfo, 'afternoonA');
		$afternoonBshift = get_shift_select($rosterinfo, 'afternoonB');
		$eveningAshift   = get_shift_select($rosterinfo, 'eveningA');
		$eveningBshift   = get_shift_select($rosterinfo, 'eveningB');
        
        //----
        // Build individual cell contents (list of shifts), leaving out unused shifts.
        $list = '';
        // Morning shifts:
        // Monday (0) doesn't have any, and only Tuesday (1) has two morning shifts.
        if ($textual_day->thisWeekDay() == 0) {
        	$list .= "";
        } elseif ($textual_day->thisWeekDay() == 1) {
        	$list .= "Morning A: $morningAshift Morning B: $morningBshift ";
        } else {
            $list = "Morning: $morningAshift ";
        }
        // Afternoon shifts:
        // Monday (0), Tuesday (1) and Thursday (3) all have two afternoon shifts.
        if ($textual_day->thisWeekDay() == 0 || $textual_day->thisWeekDay() == 1 || $textual_day->thisWeekDay() == 3) {
            $list .= "Afternoon A: $afternoonAshift Afternoon B: $afternoonBshift ";
        } else {
        	$list .= "Afternoon: $afternoonAshift ";
        }
        // Evening shifts:
        // Tuesday (1), Thursday (3), and Friday (4) have two evening shifts.
        if ($textual_day->thisWeekDay() == 1 || $textual_day->thisWeekDay() == 3 || $textual_day->thisWeekDay() == 4) {
            $list .= "Evening A: $eveningAshift Evening B: $eveningBshift ";
        }
        // End building individual cell contents.
        //----
        
        // Display day's table cell.
        if ($Day->thisDay() == date('j') && $textual_month->thisMonth() == date('m'))
            $highlightday = " style='background-color:yellow'><a name='today'></a";
        else $highlightday = "";
        $Page['body'] .= "<td$highlightday>
            <p class='monthlabel'>".$textual_month->thisMonthName()." ".$Day->thisDay()."<p>
            <p class='shiftlist'>$list</p></td>";
        
        if ($Day->isLast()) {
            $Page['body'] .= "</tr>";
        }
    }
    
    

    // Footer for the calendar
    $Page['body'] .= "<tr>";    
    foreach ($weekdays as $weekday) {
        $Page['body'] .= "<th>$weekday</th>";
    }
    $textual_month = & new Calendar_Decorator_Textual($Month);
    $Page['body'] .= "</tr>
        <tr>
          <th style='padding:1em; border-right:0'>
            <a href='6?$prev_qs'>&laquo; ".$textual_month->prevMonthName()."</a>
          </th>
          <th colspan='5' style='padding:1em; border-left: 0; border-right:0'>
            ".$textual_month->thisMonthName()." $current_year
          </th>
          <th style='padding:1em; border-left:0'>
            <a href='6?$next_qs'>".$textual_month->nextMonthName()." &raquo;</a>
          </th>
        </tr>
        <tr> 
    </table>
    <p class='submit'><input type='submit' value='Save changes' name='save' /></p>
    </form>";
    
}    
   
/**
 * Build and return the (<select> list) or (co-ordinator name with edit link).
 *
 * @param $rosterinfo Associative array of a single day's roster.
 * @param $shift Lowercase string: morningA, morningB, afternoonA, etc.
 */
function get_shift_select($rosterinfo, $shift) {
    if ($rosterinfo[$shift.'_shift']!=0) { // If shift is already filled.
        $coordie = mysql_fetch_assoc(mysql_query('SELECT * FROM people WHERE id='.$rosterinfo[$shift.'_shift']));
        $shiftselect = "<strong>".$coordie['first_name']." ".$coordie['surname']."</strong>";
        if (!$rosterinfo[$shift.'_confirmed']) {
            $shiftselect .= "<a href='6?action=edit&date=".$rosterinfo['date']."&shift=$shift' title='Edit'>[edit]</a>";
        }
        $shiftselect .= "<br />";
    } else { // If shift is empty.
	    $shiftselect = "<select name='".$shift."shifts[".$rosterinfo['date']."]'>".get_coordie_options()."</select>";
	}
	return $shiftselect;
}

/**
 * Get list of <option>s for co-ordinators.
 *
 * @return String of elements: <option value='coordie_id'>coordie_name</option>
 */
function get_coordie_options() {
	$res = mysql_query("SELECT * FROM people WHERE coordinator=1 ORDER BY first_name");
	$coordies_options = "<option value=''></option>";
	while ($coordie = mysql_fetch_assoc($res)) {
	    $coordies_options .= "<option value='".$coordie['id']."'>
	        ".$coordie['first_name']." ".$coordie['surname']."</option>";
	}
	return $coordies_options;
}

/**
 * Save a POSTed month's shifts.
 *
 * @param $shift Which shift to save/insert.
 */
function save_shifts($shift) {
	if (!empty($_POST[$shift.'shifts'])) {
	    foreach ($_POST[$shift.'shifts'] as $date=>$person_id) {
	        if ($person_id > 0) {
	            mysql_query("INSERT INTO `co-ordinators_roster` SET ".$shift.'_shift'." = ".dbesc($person_id).",
	                date = ".dbesc($date)." ON DUPLICATE KEY UPDATE ".$shift.'_shift'." = ".dbesc($person_id))
	            or die(mysql_error());
	        }
	    }
    }
}


?>