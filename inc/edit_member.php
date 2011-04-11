<?php

$Page['style'] .= "form {
	width: 90%;
	margin: auto;
	background-color: pink;
	padding: 1em;
	border: 1px solid gray;
}";

if ($_POST['save']) {
	if ($_POST['coordinator']) {
		$_POST['coordinator'] = 1;
	} else {
		$_POST['coordinator'] = 0;
	}
    $sql = "UPDATE people SET
            first_name            = ".dbesc($_POST['first_name']).",
            surname               = ".dbesc($_POST['surname']).",
            userlevel             = ".dbesc($_POST['userlevel']).",
            username              = ".dbesc($_POST['username']).",";
	if (!empty($_POST['password'])) {
		$sql .= "
			password              = SHA1(".dbesc($_POST['password'])."),
		";
	}
    $sql .= "email_address         = ".dbesc($_POST['email_address']).",
            main_phone            = ".dbesc($_POST['main_phone']).",
            other_phone           = ".dbesc($_POST['other_phone']).",
            occupation            = ".dbesc($_POST['occupation']).",
            qualifications        = ".dbesc($_POST['qualifications']).",
            home_address_line1    = ".dbesc($_POST['home_address_line1']).",
            home_address_line2    = ".dbesc($_POST['home_address_line2']).",
            home_suburb           = ".dbesc($_POST['home_suburb']).",
            home_postcode         = ".dbesc($_POST['home_postcode']).",
            home_state            = ".dbesc($_POST['home_state']).",
            home_country          = ".dbesc($_POST['home_country']).",
            postal_address_line1  = ".dbesc($_POST['postal_address_line1']).",
            postal_address_line2  = ".dbesc($_POST['postal_address_line2']).",
            postal_suburb         = ".dbesc($_POST['postal_suburb']).",
            postal_postcode       = ".dbesc($_POST['postal_postcode']).",
            postal_state          = ".dbesc($_POST['postal_state']).",
            postal_country        = ".dbesc($_POST['postal_country']).",
            membership_status     = 'full',
            coordinator           = ".dbesc($_POST['coordinator']).",
            management            = 0
            WHERE id = ".dbesc($_REQUEST['person_id']);
    $res = mysql_query($sql);
    if (mysql_affected_rows()>0) {
        $Page['body'] .= "<p>Changes saved.</p>";
    } else {
        $Page['error_message'] .= "<p>".mysql_error()."</p>";
    }
}

$res = mysql_query("SELECT * FROM people WHERE id=".dbesc($_REQUEST['person_id']));
$p = mysql_fetch_assoc($res);

$p['person_id'] = $_REQUEST['person_id'];

$Page['body'] .= get_editperson_form($p);

?>
