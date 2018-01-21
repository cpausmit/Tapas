<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Course.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

$courses = Courses::fromDb();
$activeTables = new Tables("ActiveTables");
$term = substr($activeTables->getUniqueMatchingName('Evaluations'),-5,5);
$assignments = Assignments::fromDb($term);

print '<article class="page">'."\n";
print "<h1>Active evaluations term: $term</h1>";

$list = "";
foreach ($assignments->list as $key => $assignment) {
  if (strpos($assignment->task,'-Lec-') > 0) {     // find only lecturers
    $myTask = new TeachingTask($assignment->task);
    $number = $myTask->getCourse();
    foreach ($courses->list as $key => $course) {
      if ($course->number == $number) {
        print " $course->number --> $assignment->person<br>\n";
        if ($list == "")
          $list = "$assignment->person";
        else
          $list = "$list,$assignment->person";
        break;
      }
    }
  }
}

print " ALL: $list<br>\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
