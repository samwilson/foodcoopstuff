<?php

$points_person_email = "Lucia Mayo <violetta98@gmail.com>";

require_once dirname(__FILE__).'/../config.php';

// Get list of all shifts done in the past that have not yet been confirmed
$sql = "SELECT DATE_FORMAT(date, '%a %D %b') AS formatted_date,
	morningA_shift,     morningB_shift,     afternoonA_shift,     afternoonB_shift,     eveningA_shift,     eveningB_shift,
	morningA_confirmed, morningB_confirmed, afternoonA_confirmed, afternoonB_confirmed, eveningA_confirmed, eveningB_confirmed
    FROM `co-ordinators_roster` WHERE date<=CURDATE()
    AND (    (morningA_confirmed!=1   AND morningA_shift!=0)
          OR (morningB_confirmed!=1   AND morningB_shift!=0)
          OR (afternoonA_confirmed!=1 AND afternoonA_shift!=0)
          OR (afternoonB_confirmed!=1 AND afternoonB_shift!=0)
          OR (eveningA_confirmed!=1   AND eveningA_shift!=0)
          OR (eveningB_confirmed!=1   AND eveningB_shift!=0)
        )
    ORDER BY date ASC";
$res = mysql_query($sql);
$list = "";
while ($day = mysql_fetch_assoc($res)) {
    $shifts = "";
    if ($day['morningA_shift']!=0)   $shifts  = get_coordie_name($day['morningA_shift'])." -- ";
    if ($day['morningB_shift']!=0)   $shifts  = get_coordie_name($day['morningB_shift'])." -- ";
    if ($day['afternoonA_shift']!=0) $shifts .= get_coordie_name($day['afternoonA_shift'])." -- ";
    if ($day['afternoonB_shift']!=0) $shifts .= get_coordie_name($day['afternoonB_shift'])." -- ";
    if ($day['eveningA_shift']!=0)   $shifts .= get_coordie_name($day['eveningA_shift'])." -- ";
    if ($day['eveningB_shift']!=0)   $shifts .= get_coordie_name($day['eveningB_shift'])." -- ";
    $list .= "".$day['formatted_date'].":   -- $shifts\n";
}

// Send that list to Points Person, and confirm all past shifts.
$subject = "Co-ordinator points to be allocated";
$message = "Here is this week's list of which coordinator did which shift:\n\n$list

You can view the roster at http://anu.foodco-op.com/stuff\n";
$headers = "From: Food Co-op website admin <sam@archives.org.au>"."\r\n";
mail($points_person_email, $subject, $message, $headers);
mail('sam@archives.org.au', $subject, $message, $headers);
mail('anu@foodco-op.com', $subject, $message, $headers);

mysql_query("UPDATE `co-ordinators_roster` SET morningA_confirmed=1   WHERE date<=CURDATE() AND morningA_shift!=0");
mysql_query("UPDATE `co-ordinators_roster` SET morningB_confirmed=1   WHERE date<=CURDATE() AND morningB_shift!=0");
mysql_query("UPDATE `co-ordinators_roster` SET afternoonA_confirmed=1 WHERE date<=CURDATE() AND afternoonA_shift!=0");
mysql_query("UPDATE `co-ordinators_roster` SET afternoonB_confirmed=1 WHERE date<=CURDATE() AND afternoonB_shift!=0");
mysql_query("UPDATE `co-ordinators_roster` SET eveningA_confirmed=1   WHERE date<=CURDATE() AND eveningA_shift!=0");
mysql_query("UPDATE `co-ordinators_roster` SET eveningB_confirmed=1   WHERE date<=CURDATE() AND eveningB_shift!=0");

function get_coordie_name($id) {
    $coordie = mysql_fetch_assoc(mysql_query("SELECT * FROM people WHERE id=$id"));
    return $coordie['first_name']." ".$coordie['surname'];
}

?>
