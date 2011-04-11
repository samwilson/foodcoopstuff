<?php

if ($User['logged-in']) header("Location:".$_SERVER['SCRIPT_NAME']);

if ( isset($_POST['username']) || isset($_POST['password']) ) {
	
	$sql = ("SELECT * FROM people
			WHERE username = ".dbesc($_POST['username'])."
			AND password = SHA1(".dbesc($_POST['password']).")
			LIMIT 1");
	$result = mysql_query($sql);
	if (mysql_num_rows($result) == 1) {
		$User = mysql_fetch_assoc($result);
		$_SESSION['uid'] = $User['id'];
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['logged-in'] = TRUE;
		$User['logged-in'] = true;
		$current_session_id = dbesc(session_id());
		$current_ip = dbesc($_SERVER['REMOTE_ADDR']);
		$sql = ("UPDATE people SET last_login=NOW(),
				login_count=(login_count+1),
				current_session_id = $current_session_id,
				current_ip = $current_ip
				WHERE username='".$User['username']."' LIMIT 1");
		if (!mysql_query($sql)) die(mysql_error());
		$logincount = $User['login_count'] + 1;
		
		// Do log entry.
		mysql_query("INSERT INTO log SET what='login', who='".$User['username']."'");
		
		if ($User['level'] <= 2) header("Location:".$_SERVER['SCRIPT_NAME']);
		
		$Page['body'] .= ("<p>Hello ".$User['first_name']."</p><p>You are now
			logged in.  You have logged in $logincount times and the last time
			was on ".$User['last_login'].".  You will remain logged in until you
			quit your browser.</p>");
		
	} else {
		$Page = array();
		$Page['parent_id'] = 1;
		$Page['title'] = "Login Failed";
		$Page['error_message'] = "Your login attempt failed, please try again.".mysql_error();
		$Page['needs_login'] = true;
	}


} else {
	$Page['body'] = "";
	$Page['needs_login'] = TRUE;
}

?>
