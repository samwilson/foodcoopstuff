<?php

$sql = "SELECT * FROM people WHERE email_address!=''";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$email_addresses .= $row['email_address']."\n";
}
$Page['body'] .= "<textarea rows='40' cols='150' style='margin:auto; display:block'>$email_addresses</textarea>";

?>