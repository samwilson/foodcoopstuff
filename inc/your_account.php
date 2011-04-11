<?php

$Page['style'] .= "form {
	width: 90%;
	margin: auto;
	background-color: lightblue;
	padding: 1em;
	border: 1px solid gray;
}";

if ($_POST['save']) {
    $password_error = FALSE;
    if ($_POST['password']!=='') {
        //if ($_POST['password']!=$_POST['password_verification']) {
        //    $Page['error_message'] .= "<p>Your new password doesn't match it's verification.</p>";
        //    $password_error = TRUE;
        //} else {
            $password_line = "password = SHA1(".dbesc($_POST['password'])."),";
        //}
    }
    if (!$password_error) {
        $sql = "UPDATE people SET
                first_name            = ".dbesc($_POST['first_name']).",
                surname               = ".dbesc($_POST['surname']).",
                $password_line
                email_address         = ".dbesc($_POST['email_address']).",
                main_phone            = ".dbesc($_POST['main_phone']).",
                other_phone            = ".dbesc($_POST['other_phone']).",
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
                postal_country        = ".dbesc($_POST['postal_country'])."
                WHERE id = ".dbesc($User['id']);
        $res = mysql_query($sql);
        if (mysql_affected_rows()>0) {
            $Page['body'] .= "<p>Changes saved.</p>";
        } else {
            $Page['error_message'] .= "<p>".mysql_error()."</p>";
        }
    }
}


$res = mysql_query("SELECT * FROM people WHERE id=".dbesc($User['id']));
$p = mysql_fetch_assoc($res);
$p['person_id'] = $User['id'];
$Page['body'] .= get_editperson_form($p);

?>
