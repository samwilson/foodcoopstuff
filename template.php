<?php
header("Content-Type: text/html; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title><?php echo $Page['title']; ?> &laquo; FOODCOOP STUFF</title>
  <link rel="stylesheet" type="text/css" href="main.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="print.css" media="print" />
  <style type='text/css'>
	<?php echo $Page['style']; ?>
  </style>
  <script type='text/javascript'>
	<?php echo $Page['script']; ?>
  </script>
</head>
<body onLoad="init()">

<p class='tools'>
    <?php if ($User['logged-in']) echo "<a href='11'>[".$User['first_name']." ".$User['surname']."]</a> "; ?>
    <?php if ($User['userlevel']==10) echo "<a href='3?edit_id=".$Page['id']."'>[edit this page]</a> <a href='3'>[new page]</a> "; ?>
    <?php if ($User['logged-in']) echo "<a href='4'>[logout]</a>"; ?>
</p>

<p class='urhere'><?php echo $Page['urhere']; ?></p>

<hr />

<div id="content">
<?php

if ($Page['error_message']) {
	print("<div class='error'>".$Page['error_message']."</div>");
}

if ($Page['needs_login'] || ($Page['id']==1 && !$User['logged-in'])) {
	print("<div class='login-form'><form action='2' method='post'>
		<table>
		<tr><th>Username:</th><td><input type='text' name='username' /></td></tr>
		<tr><th>Password:</th><td><input type='password' name='password' /></td></tr>
		<tr><th></th><td class='submit'><input type='submit' value='Log In' name='op' id='submit' /></td></tr>
		<tr><td colspan='2'>Forgotten your password?  <a href='12'>Request a new one.</a></td></tr>
		</table></form></div>");
}

if (isset($Page['formatted_body'])) echo $Page['formatted_body'];
else echo $Page['body'];

if (isset($Page['toc'])) echo $Page['toc'];

?>

</div><!-- end div#content -->

<hr />

<div id="footer">
  <p>This <em>Food Co-op Stuff</em> is managed by Sam Wilson.  If you have any
  questions about this website (not other stuff) then please do hasten over to 
  our <a href="http://issues.possumpalace.org/flyspray/index.php?project=12">
  Possum-Powered Bug Tracking System</a> to ask them!  We <em>relish</em> the
  receipt of feature requests, bug reports, and general cries for help &mdash;
  we really do!</p>
</div><!-- end div#footer -->

</body>
</html>


