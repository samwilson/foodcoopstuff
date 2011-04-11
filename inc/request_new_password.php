<?php

$Page['body'] .= "<div style='width:50%; margin:auto; text-align:justify'>";

if ($_POST['username']) {
    $sql = "SELECT * FROM people WHERE username=".dbesc($_POST['username'])." LIMIT 1";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) == 1) {
        $p = mysql_fetch_assoc($res);
        $newpwd = genpwd();
        mysql_query("UPDATE people SET password=SHA1('$newpwd') WHERE username=".dbesc($_POST['username']));
        $to      = $p['email_address'];
        $subject = 'New password for the Food Co-op Stuff';
        $message = "
        	You requested a new password for the Food Coop Stuff; it is: $newpwd
        	(your username, by the way, is ".$p['username'].")
        	Log in at http://anu.foodco-op.com/stuff
        ";
        $headers = 'From: webmaster@anu.foodco-op.com'."\r\n";
        if (!mail($to, $subject, $message, $headers)) {
            $Page['error_message'] .= "<p>Your password was reset, but an email couldn't be sent.  Please try again.</p>";
        } else {
            $Page['body'] .= "<p style='text-align:center'>An email has been sent to you containing your new password.</p>";
        }
    } else {
        $Page['body'] .= "<p>That username is not registered.  <a href='12'>Try again</a>.</p>";
    }
} else {

    $Page['body'] .= "<form action='12' method='post'>
        <p>Enter your username in the box below to have a new password sent to you. 
        Your username is usually your full name with no spaces (e.g. Fred Smith
        would be fredsmith) and for some of you it's just your first name.</p>
        <p><input type='text' name='username' style='width:100%' /></p>
        <p class='submit'><input type='submit' name='newpwd' value='Get new password' /></p>
    </form>";

}

$Page['body'] .= "</div>";

?>