<?php

$p = mysql_fetch_assoc(mysql_query("SELECT * FROM people WHERE id=".dbesc($_GET['person_id'])." LIMIT 1"));

$Page['body'] .= "<p style='width:40%; margin:auto; border:1px solid black; padding:1em'>
   <strong>".$p['first_name']." ".$p['surname']."</strong><br />
   ".$p['main_phone']." ";
   
if ($p['other_phone']) $Page['body'] .= "or ".$p['other_phone'];

$Page['body'] .= "<br /><a href='mailto:".$p['email_address']."'>".$p['email_address']."</a><br />
    ".$p['home_address_line1']."<br />
    ".$p['home_address_line']."<br />
    ".$p['home_suburb']." ".$p['home_postcode']."<br />
    ".$p['home_state']." ".$p['home_country']."
    </p>";

?>