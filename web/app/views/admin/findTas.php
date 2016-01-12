<?php

// make sure we have an admin
include("app/views/admin/header.php");

if (! isMaster()) {
  exitAccessError();
}

print '<article class="page">'."\n";
print '<hr>'."\n";
print '<h1>Parse rough TA List</h1>'."\n";
print '<hr>'."\n";

// read rough list
$taCandidates = readAccessFile('access/taList');

// connect to our database
$link = getLink();

foreach ($taCandidates as $key => $taCandidate) {
  print '<hr>'."\n";
  print " Candidate: $taCandidate<br>\n";

  $f = explode(" ",$taCandidate);

  if (sizeof($f) < 2)
    continue;

  $candFirstName = $f[0];
  $candLastName = $f[sizeof($f)-1];

  $query = "select * from Students where LastName='" . $candLastName . "'";
  //print " SQL: $query";
  $statement = $link->prepare($query);
  $statement->execute();
  $statement->bind_result($firstName,$lastName,$email,$advisorEmail,$supervisorEmail,$year,$divison,
                          $research);
  while ($statement->fetch()) {
    print "$email -- $firstName $lastName<br>\n";
  }
}
print '<hr>'."\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
