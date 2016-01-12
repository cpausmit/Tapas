<?php

include("app/views/ta/header.php");

print '<article class="page">'."\n";
print '<h1>TA Functions</h1>'."\n";

if (isTa() || isMaster()) {
  print '<p> Congratulations! You are going to be a TA this coming term.'."\n";
  print '    Work on your TA preferences:</p>'."\n";
  print '<ul class="cp-list">'."\n";
  print '<li> <a href="/select">select</a>'." your TA preferences for the coming term.\n";
  print "      Please, make a distinct choice for each preference.\n";
  print "      Use the submit button and check that the choices were accepted.\n";
  print '<li> <a href="/show">show</a>'." your present choice for the preferences. Here\n";
  print "      we will go to the database and present what you registered.\n";
  print '<li> <a href="/remove">remove</a>'." the presently registered preferences. Ready to\n";
  print "      make another set of choices. There will be no confirmation, plain and simple\n";
  print "      remove.\n";
  print "</ul>\n";
  print ' '."\n";
  print ' <hr>'."\n";
  print ' Thank you! for being available to help with the important task of teaching in the';
  print ' department.';
  //print ' Please remember, teaching is rewarding, but can also be hard at times for';
  //print ' TAs and for professors. Keep a positive attitude, it helps everybody!<br><br>'."\n";
  //print '<center><img class="rounded" src="/img/phd120610s.gif"></center>'."\n";
}
else {
  print '<p> You are not on the TA list. This could be a mistake'."\n";
  print '    <a href="/contact">my coordinates</a>.'."\n";
}

include("app/views/ta/footer.php");

?>
