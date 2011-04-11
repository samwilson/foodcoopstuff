<?php

$Page['style'] .= ".alt {background-color:#ddd}
table {width:80%; margin:auto}";

$Page['body'] .= "<table><tr>
    <th>Name</th>
    <th>Phone</th>
    <th>Email</th>
</tr>";
$res = mysql_query("SELECT * FROM people WHERE coordinator=1 ORDER BY first_name");
while($coordie = mysql_fetch_assoc($res)) {
    if ($alt=='') $alt=' class="alt"'; else $alt='';
    $Page['body'] .= "<tr$alt>
        <td><a href='13?person_id=".$coordie['id']."'>".$coordie['first_name']." ".$coordie['surname']."</a></td>
        <td>".$coordie['main_phone']."</td>
        <td><a href='mailto:".$coordie['email_address']."'>".$coordie['email_address']."</a></td>
    </tr>";
}
$Page['body'] .= "</table>";

?>