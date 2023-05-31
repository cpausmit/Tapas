<?php
include("app/views/header.php");

print '<article class="page">'."\n";
print '<h1>Who are You to this web site?</h1>'."\n";

print '<hr>'."\n";

// set default timezone
date_default_timezone_set('America/New_York');

//if (@$_SERVER['SSL_CLIENT_S_DN_CN']) {
//  print '<p>You are <b>' . $_SERVER['SSL_CLIENT_S_DN_CN'] . '</b>'
//  . ' with email <b>' . $_SERVER['SSL_CLIENT_S_DN_Email'] . '</b>.</p>'
//  . '<p>Certificate issued by <b>' . $_SERVER['SSL_CLIENT_I_DN_O']
//  . '</b> correctly installed in your browser.</p>'
//  . '<p>Your certificate will expire on <b>' . $_SERVER['SSL_CLIENT_V_END'] . '</b>.</p>'."\n";
  
if (@$_SERVER['Shib-Session-Expires']) {
  print '<p>You are <b>' . $_SERVER['displayName'] . '</b>'
  . ' with email <b>' . $_SERVER['eppn'] . '</b>.</p>'
  . '<p>Certificate issued by <b>' . $_SERVER['Shib-Identity-Provider']
  . '</b> correctly installed in your browser.</p>'
  . '<p>Your certificate will expire on <b>' . date("c", $_SERVER['Shib-Session-Expires']) . '</b>.</p>'."\n";
//  . '<p>Your certificate will expire on <b>' . gmdate("Y-m-d\TH:i:s\Z", $_SERVER['Shib-Session-Expires']) . '</b>.</p>'."\n";
//  . '<p>Your certificate will expire on <b>' . $_SERVER['Shib-Session-Expires'] . '</b>.</p>'."\n";

  print '<p>You are using <b>' . $_SERVER['HTTP_USER_AGENT'] . '</b><br> from <b>'
  . $_SERVER['REMOTE_ADDR'] . '</b> (<b>' . gethostbyaddr($_SERVER['REMOTE_ADDR'])
  . '</b>).</p>'."\n";
}
else {
  print 'No certificate has been detected. Please ensure you are accessing'."\n";
  print '<a href="https://t3serv012.mit.edu:443/me.php">' .
    'http<b>s</b>://t3serv012.mit.edu<b>:443</b>/me.php</a>.'."\n";
}

print '<p>You are in the following TAPAS lists: <b>'."\n";
if (isTa()) {
  print ' TAs'."\n";
}
if (isTeacher()) {
  print ' Teachers'."\n";
}
if (isAdmin()) {
  print ' Admins'."\n";
}
if (isMaster()) {
  print ' Master'."\n";
}
print '</b>.</p>'."\n";
print '<hr>'."\n";

if (isMaster()) {
  foreach ($_SERVER as $key_name => $key_value) {
    print $key_name . " = " . $key_value . "<br>";
  }
}
print '</article>'."\n";

include("app/views/footer.php");

?>
