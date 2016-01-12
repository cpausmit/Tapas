<?php

include("app/views/header.php");

if (isTeacher() || isMaster()) {
  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px; color: darkblue; background-color: lightblue" class="transbox">'."\n";
  print '&nbsp; Teachers:'."\n";
  print '&nbsp; <a href="/teacher"> <span class="fa fa-home">Home</span></a> &nbsp;'."\n";
  print ' <a href="/enter">enter</a>'."\n";
  print ' <a href="/review">review</a>'."\n";
  print '</div>'."\n";
}
else {
  print ' Not a teacher this term.<br>';
}

?>
