<?php

$sql = "SELECT * FROM regular_shifts";
$res = mysql_query($sql);
while ($shift = msyql_fetch_assoc($res)) {
	$Page['body'] .= "<tr>";
	
	$Page['body'] .= "</tr>";
}

?>