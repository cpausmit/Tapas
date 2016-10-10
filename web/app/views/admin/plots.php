<?php

include("app/views/admin/header.php");
include("app/models/TeachingTask.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) {
  exitAccessError();
}

// command line arguments
//$option = $_GET['option'];  // this is the input course number

print '<article class="page">'."\n";
print '<h1>Assignment History</h1>';
print '<div style="width:95%;">'."\n";
print '<input type="button" onclick="toggleType(\'line\')" value="line">'."\n";
print '<input type="button" onclick="toggleType(\'bar\')" value="bar">'."\n";
print '</div><br>'."\n";
print '<div id="canvasDivisions" style="width:95%;"></div><br>'."\n";
print '<div id="canvasTotals"    style="width:95%;"></div>'."\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

print "\n".'<script type="text/javascript">'."\n";
print 'initPlots();'."\n";
print '</script>'."\n";

?>
