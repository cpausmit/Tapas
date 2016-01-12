<?php

include("app/views/admin/header.php");
include("app/models/TeachingTask.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line arguments
$number = $_GET['number'];  // this is the input course number

// conncet with our database
$link = getLink();

$tables = findTables($link,'Assignments');
$names = findStudentNames($link);
$courseName = findCourseName($link,$number);

print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Course Task Summary Page</h1>\n<h2>$number $courseName</h2>";
print "<hr>\n";

print '<table>';
foreach ($tables as $key => $table) {
  $query = "select * from " . $table . " where Task like '%-" . $number . "-Ta%'";
  $statement = $link->prepare($query);
  $statement->execute();
  $statement->bind_result($taskId,$person);
  while ($statement->fetch()) {

    $name = "";
    if (! isset($names[$person])) {
      if ($person == '')
	$name = 'NOT ASSIGNED';
      else
	print " person not found: $person<br>\n";
    }
    else
      $name = $names[$person];

    // print the result
    print '<tr><td>' . $name . '&nbsp;</td><td>&nbsp;'
      . '<a href="/showTaSummary?email=' . $person . '">' . $person . '</a>&nbsp;</td><td>';
    $task = new TeachingTask($taskId);
    $task->printTask();
    print "</td></tr>\n";
  }
}
print '</table>';
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
