<?php

// global variable to manage the result of the form input
$GLOBALS['COMPLETE'] = 2;

// make sure we are dealing with a administrator
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Semester.php");
include_once("app/models/Course.php");
include_once("app/models/CourseResource.php");

// Find the term we want to plan
function getPostVariable($variableName)
{
  // read complete courses table
  $variable = 'undefined';
  if (array_key_exists($variableName,$_POST))
    $variable = $_POST[$variableName];
  else
    $GLOBALS['COMPLETE'] = 0;

  return $variable;
}

function printTermForm($semesters)
{
  print '<table>';
  print '<form  action="/generateAssignments" method="post">'."\n";
  print '<tr>';
  print '<td>';
  print '  Term:&nbsp;'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="term">'."\n";
  print "<option value=\"\">term ?</option>";
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

print "<article class=\"page\">\n";

// get all relevant info from the database
$semesters = Semesters::fromDb();
$courses = Courses::fromDb();
$term = getPostVariable('term');
$courseResources = CourseResources::fromDb($term);

if (!$GLOBALS['COMPLETE']) {                // term parameter was not available
  print "<article class=\"page\">\n";
  print "<h1>Which Term?</h1>\n";
  print "<hr>\n";
  printTermForm($semesters);
  // footer
  print "<hr>\n";
  print '</article>'."\n";
  include("app/views/admin/footer.php");
  exit("");
}
else {                                     // term was set
  print "<article class=\"page\">\n";
  if (! isset($semesters->list[$term])) {
    print "<h1>ERROR</h1>\n";
    print "This term ($term) is not in our database.\n";
  }
  // start the remaining html page
  print "<h1>Different Term?</h1>\n";
  print "<hr>\n";
  printTermForm($semesters);
  print "<hr>\n";
  print "<h1>Adding the following Assignments (Term: $term)</h1>\n";
  print "<hr>\n";
  if (isset($semesters->list[$term]))
    $courseResources->registerAssignments();
}

// footer
print "<hr>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
