<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Course.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

$db = Dbc::getReader();

$activeTables = new Tables($db,"ActiveTables");
$evaluationsTable = $activeTables->getUniqueMatchingName('Evaluations');
$term = substr($evaluationsTable,-5,5);
$courses = Courses::fromDb($db);

print '<article class="page">'."\n";
print "Active evaluations table: $evaluationsTable</p>";

$list = "";
$sql = "select Person,Task from Assignments$term where Task like '%-Lec-%' order by Task";
$rows = $db->query($sql);
foreach ($rows as $key => $row) {
  $email = $row[0];
  $taskId = $row[1];
     
  $myTask = new TeachingTask($taskId);
  $number = $myTask->getCourse();
  foreach ($courses->list as $key => $course) {
    if ($course->number == $number) {
      print " $course->number --> $email<br>\n";
      if ($list == "")
        $list = "$email";
      else
        $list = "$list,$email";
      break;
    }
  }
}

print " ALL: $list<br>\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
