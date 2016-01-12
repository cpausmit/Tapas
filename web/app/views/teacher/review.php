<?php

include("app/views/teacher/header.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Review TA Evaluations</h1>'."\n";
print ' '."\n";

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

// connect to our database
$link = getLink();
$active = findActiveTable($link,'Evaluations');
print "Active evaluations table: $active[0]</p>";
$query = "select TeacherEmail,TaEmail,EvalText,Award,Citation from $active[0] "
  . "where TeacherEmail='$email'";
$statement = $link->prepare($query);
$rc = $statement->execute();
if (! $rc) {
  $errNum = mysqli_errno($link);
  $errMsg = mysqli_error($link);
  print " ERROR - could not get evaluations: ErrNo=$errNum: $errMsg\n";
  exit();
}
$statement->bind_result($teacherEmail,$taEmail,$evalText,$award,$citation);
$empty = true;
while ($statement->fetch()) {
  $empty = false;
  print '<hr>';
  print '<p>';
  print "<b>Evaluee:  </b> $taEmail<br>\n";
  print "<b>AwardProposed:</b> $award<br>\n";
  print "<b>Proposed Citation:</b> $citation<br>\n";
  print "<b>Evaluation:</b><br> $evalText</p>\n";
  print '<p>';
}

if ($empty)
  print ' No evaluations found in this term.';
  
print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
