<?php

include("app/views/teacher/header.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Evaluation.php");
include_once("app/models/Student.php");
include_once("app/models/Tables.php");

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
$studentEmail = 'undefined';
if (isset($_POST['ta']))
  $studentEmail = $_POST['ta'];
else
  print " ERROR no student name.<br>\n";

// connect to our database
$db = Dbc::GetReader();
$students = Students::fromDb($db);
$student = $students->list[$studentEmail];
$name = $student->lastName.", ".$student->firstName;

// find active evaluations table
$activeTables = new Tables($db,"ActiveTables");
$term = substr($activeTables->getUniqueMatchingName('Evaluations'),-5,5);
$evaluations = Evaluations::fromDb($db,$term);

// not nice - key needs to be contructed somewhere else
$key = "$term:$email:$studentEmail";

if (isset($evaluations->list[$key]))
  $evaluation = $evaluations->list[$key];
else
  $evaluation = Evaluation::fresh();

print '<article class="page">'."\n";
print '<h1>Enter TA Evaluation</h1>'."\n";
print ' '."\n";

// start the form
print '<p>';
print "<form action=\"/record?ta=$studentEmail\" method=\"post\">\n";
print "Write your evaluation for $name below.\n";
print '<textarea  style="font-family: arial, verdana, sans-serif; font-size: 20px;'
    . ' color: black; background-color: white" name="Evaluation" rows=8 cols=80>'
    . $evaluation->evalText .'</textarea>';
if ($evaluation->award == 0)
  print '<input type="checkbox" name="Award" value="1"> Propose award?';
else
  print '<input type="checkbox" name="Award" value="1"checked> Propose award?';
print '&nbsp; if yes, please add a short citation for the potential award below.<br>';
print '<textarea style="font-family: arial, verdana, sans-serif; font-size: 20px;'
    . ' color: black; background-color: white" name="Citation" rows=1 cols=40>'
    . $evaluation->citation . '</textarea>';
print '<input type="submit" value="submit your evaluation" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
