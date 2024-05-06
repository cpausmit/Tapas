<?php

// make sure we are dealing with a administrator
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Semester.php");
include_once("app/models/Tables.php");
include_once("app/models/Course.php");
include_once("app/models/CourseResource.php");

// Find the term we want to plan
function findTerm($semesters)
{
  $term = '';
  if (array_key_exists('term',$_POST)) {    // find the term for the planning assignments table
    $term = $_POST['term'];
  }
  else {
    $planningTables = new Tables("PlanningTables");
    $assignmentTable = $planningTables->getUniqueMatchingName('Assignments');
    $term = substr($assignmentTable,-5,5);
  }
  return $term;
}

function printTermForm($term,$semesters)
{
  print '<table>';
  print '<form  action="/generateAssignments" method="post">'."\n";
  print '<input type="hidden" name="term" value="'.$term.'" />';
  print '<tr>';
  print '<td>';
  print '  Term:&nbsp;'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="term">'."\n";
  print "<option value=\"\">".$term."</option>";
  foreach ($semesters->list as $key => $semester)
    print "<option value=\"$key\"> $key </option>";
  print '    </select></td>'."\n";
  print '</td>';
  print '<td><input type="submit" value="select" />'."\n";
  print '</td></tr>';
  print '</table>';
  print '</form>'."\n";
}

//==================================================================================================
// M A I N
//==================================================================================================

// get all relevant info from the database
$semesters = Semesters::fromDb();
$courses = Courses::fromDb();
$term = findTerm($semesters);
print "TERM: $term";
$courseResources = CourseResources::fromDb($term);

print "<article class=\"page\">\n";
if (! isset($semesters->list[$term])) {
  print "<h1>ERROR</h1>\n";
  print "This term ($term) is not in our database.\n";
}

// start the remaining html page
print "<h1>Generating Assignments</h1>\n";
print "<p>Different Term?</p>\n";
print "<hr>\n";
printTermForm($term,$semesters);
print "<hr>\n";
print "<h1>Adding the following Assignments (Term: $term)</h1>\n";
print "<hr>\n";
if (isset($semesters->list[$term]))
  $courseResources->registerAssignments();

// footer
print "<hr>\n";
print '</article>'."\n";
include("app/views/admin/footer.php");

?>
