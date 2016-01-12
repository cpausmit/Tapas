<?php

include("app/views/teacher/header.php");

print '<article class="page">'."\n";
print '<h1>Teacher Functions</h1>'."\n";

if (isTeacher() || isMaster()) {
  print '<p> Please enter your evaluation:</p>'."\n";
  print '<ul class="cp-list">'."\n";
  print '<li> <a href="/enter">enter</a>'." your TA evaluation for the last term.\n";
  print "      Please, check the button if you are suggesting an award and include\n";
  print "      a citation that we can use for the award. Use the submit button and\n";
  print "      check that the choices were accepted.\n";
  print '<li> <a href="/review">review</a>'." your present evaluations. Here\n";
  print "      we will go to the database and present what you submitted. To edit.\n";
  print '      go back to \'enter\' it will give you your last text as starting point.';
  print "</ul>\n";
  print ' '."\n";
  print ' <hr>'."\n";
  print ' Thank you! for the input, this is very useful for future assignments.';
}
else {
  print '<p> You are not on the Teachers list. This could be a mistake'."\n";
  print '    <a href="/contact">my coordinates</a>.'."\n";
}

include("app/views/teacher/footer.php");

?>
