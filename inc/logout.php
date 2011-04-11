<?php

// Do log entry.
mysql_query("INSERT INTO log SET what='logout', who='".$User['username']."'");

mysql_query("UPDATE people SET current_ip='', current_session_id='' WHERE id=".dbesc($User['id']));

$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
   setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();
$User = null;
$User['userlevel'] = 0;
$Page['needs_login'] = true;

?>