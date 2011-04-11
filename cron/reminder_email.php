<?php

require_once dirname(__FILE__).'/../config.php';

// Make sure the next seven or so days exist in the DB.
for ($days=0; $days <= 8; $days++) {
    $sql = "SELECT * FROM `co-ordinators_roster` WHERE DATE_ADD(CURDATE(),INTERVAL $days DAY) = date";
    $num = mysql_num_rows(mysql_query($sql));
    if ($num<1) {
        mysql_query("INSERT INTO `co-ordinators_roster` SET date=DATE_ADD(CURDATE(),INTERVAL $days DAY)");
    }
}


$sql = "SELECT DATE_FORMAT(date, '%a %D %b') AS formatted_date,
               DATE_FORMAT(date, '%a') AS weekday,
               morningA_shift,     morningB_shift,     afternoonA_shift,     afternoonB_shift,     eveningA_shift,     eveningB_shift,
			   morningA_confirmed, morningB_confirmed, afternoonA_confirmed, afternoonB_confirmed, eveningA_confirmed, eveningB_confirmed
		FROM `co-ordinators_roster`
		WHERE DATE_ADD(CURDATE(),INTERVAL 7 DAY) >= date AND date>=CURDATE()
		ORDER BY date ASC";
$res = mysql_query($sql);
$list = "";
while ($day = mysql_fetch_assoc($res)) {
    $shifts = "";
    if (!$day['morningA_shift'] && $day['weekday']!='Mon') $shifts  = " Morning A (10-1)";
    if (!$day['morningB_shift'] && $day['weekday']=='Tue') $shifts  .= " Morning B (9:30-12:30)";
    if (!$day['afternoonA_shift']) $shifts .= " Afternoon A (1-4) ";
    if (!$day['afternoonB_shift'] && ($day['weekday']=='Mon' || $day['weekday']=='Tue' || $day['weekday']=='Thu') ) $shifts  .= " Afternoon B (1-4) ";
    if (!$day['eveningA_shift']   && ($day['weekday']=='Tue' || $day['weekday']=='Thu' || $day['weekday']=='Fri') ) $shifts  .= " Evening A (4-7) ";
    if (!$day['eveningB_shift']   && ($day['weekday']=='Tue' || $day['weekday']=='Thu' || $day['weekday']=='Fri') ) $shifts  .= " Evening B (4-7) ";
    if ($shifts) $list .= "".$day['formatted_date'].":   $shifts\n";
}


if (!empty($list)) {

    $subject = "Emtpy shifts for the next 7 days.";
    $headers = "From: Rotanidrooc <sam@co-operista.com>"."\r\n";
    $res = mysql_query("SELECT * FROM people WHERE coordinator=1 AND email_address!=''");
    while ($coordie = mysql_fetch_assoc($res)) {
        $message = "Hi ".$coordie['first_name'].",

The following shifts are still vacant:

$list

You can put your name down for any of these shifts by logging
in at http://anu.foodco-op.com/stuff/2 with your username and
password.  Your username is '".$coordie['username']."'.
If you can't rememer your password, you can request a new one
from the login page.

I hope you're having an absolutely wizard day!

Your most 'umble servant,

Rotanidrooc,
February 18th, 2009.

P.S.  If there is anything wrong with this roster system,
      please log a support ticket in our Possum-Powered Bug-
      Tracking System:
      http://issues.possumpalace.org/flyspray/index.php?project=12
";
        mail($coordie['email_address'], $subject, $message, $headers);
    }
}

?>
