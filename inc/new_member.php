<?php

if (!empty($_POST['save'])) {

	/*
    // Construct username.  The person's full name, suffixed with a
    // number in the case of a clash or clashes.
    $base_username = strtolower($_POST['first_name'].$_POST['surname']);
    // Start suffix numbering at two so that it reflects number of
    // people with that name.
    $n = 2;
    $username = $base_username;
    while (0 < mysql_num_rows(mysql_query("SELECT * FROM people WHERE username=".dbesc($username)))) {
        $username = $base_username.$n;
        $n++;
    }
    $password = genpwd();
    */
    
    $sql = "INSERT INTO people SET
            username              = ".dbesc($_POST['username']).",
            password              = SHA1(".dbesc($_POST['password'])."),
            userlevel             = ".dbesc($_POST['userlevel']).",
            first_name            = ".dbesc($_POST['first_name']).",
            surname               = ".dbesc($_POST['surname']).",
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
            postal_country        = ".dbesc($_POST['postal_country']).",
            membership_status     = 'full',
            coordinator           = 1,
            management            = 0";
    mysql_query($sql);
    $Page['error_message'] .= "<p>".mysql_error()."</p>";
    $Page['body'] .= "<p>Member added.</p>";
}

else {

	$Page['style'] .= "form {
		width: 90%;
		margin: auto;
		background-color: pink;
		padding: 1em;
		border: 1px solid gray;
	}";
	
	$Page['body'] .= get_editperson_form(null);
	
}

?>
