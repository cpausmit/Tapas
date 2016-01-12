<?php

include("app/views/header.php");

if (isTa() || isMaster()) {
  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px; color: darkgreen; background-color: lightyellow" class="transbox">'."\n";
  print '&nbsp; TAs:'."\n";
  print '&nbsp; <a href="/ta"> <span class="fa fa-home">Home</span></a> &nbsp;'."\n";
  print ' <a href="/select">select</a>'."\n";
  print ' <a href="/show">show</a>'."\n";
  print ' <a href="/remove">remove</a>'."\n";
  print '</div>'."\n";
}
else {
  print ' No TA this term.<br>';
}

?>
