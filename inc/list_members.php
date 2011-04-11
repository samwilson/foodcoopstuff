<?php 

$Page['style'] .= "table {width: 100%; border:1px solid black; border-collapse:collapse}
th {border:1px solid black}
tr.alt {background-color:#ddd}
table input {border:0; width:100%}";

$sql = "SELECT * FROM people WHERE 1=0";
if ($_POST['username']) $sql .= " OR username LIKE ".dbesc('%'.$_POST['username'].'%');
if ($_POST['name']) $sql .= " OR first_name LIKE ".dbesc('%'.$_POST['name'].'%');
if ($_POST['name']) $sql .= " OR surname LIKE ".dbesc('%'.$_POST['name'].'%');
if ($_POST['email']) $sql .= " OR email_address LIKE ".dbesc('%'.$_POST['email'].'%');
if ($_POST['membership_status']) $sql .= " OR membership_status = ".dbesc($_POST['membership_status']);

$res = mysql_query($sql);

$Page['body'] .= "
    <p><strong>".mysql_num_rows($res)." people shown.</strong>
    To search, enter your query terms in this table.  To show everyone, enter a
    <code>%</code> in any of the fields.</p>
    <form action='".$Page['id']."' method='post'>
    <table><tr>
    <th>Username</th>
    <th>Name</th>
    <th>Email address</th>
    <th>Phone (primary)</th>
    <th>Actions</th>
</tr>";
while ($person = mysql_fetch_assoc($res)) {
    if ($alt=='') $alt=' class="alt"'; else $alt='';
    $Page['body'] .= "<tr$alt>
        <td>".$person['username']."</td>
        <td>".$person['surname'].", ".$person['first_name']."</td>
        <td><a href='mailto:".$person['email_address']."'>".$person['email_address']."</a></td>
        <td>".$person['main_phone']."</td>
        <td><a href='9?person_id=".$person['id']."'>[EDIT]</a></td>
    </tr>";
}
$Page['body'] .= "<tr>
    <th><input type='text' name='username' /></th>
    <th><input type='text' name='name' /></th>
    <th><input type='text' name='email' /></th>
    <th> &mdash; </th>
    <th><input type='submit' name='search' value='Search' /></th>
</tr></table></form>";

?>