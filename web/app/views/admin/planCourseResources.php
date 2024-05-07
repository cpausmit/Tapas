<?php

// make sure we are dealing with a administrator
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Dbc.php");
include_once("app/models/Semester.php");
include_once("app/models/Tables.php");
include_once("app/models/Course.php");
include_once("app/models/CourseResource.php");

function getPostVariable($variableName)
{
  // read complete courses table
  $variable = 'undefined';
  if (array_key_exists($variableName,$_POST))
    $variable = $_POST[$variableName];
  return $variable;
}

// Find the term we want to plan
function findTerm($semesters)
{
  $term = getPostVariable('term');
  if (strcmp($term,'undefined') == 0) {       // assign the correct default
    // find the term for the planning assignments table
    $planningTables = new Tables("PlanningTables");
    $assignmentTable = $planningTables->getUniqueMatchingName('Assignments');
    $term = substr($assignmentTable,-5,5);
  }
  return $term;
}

function printNum($min,$max)
{
  // Generate the number option pannel from minimum to maximum

  for ($i=$min; $i<=$max; $i++) {
    print "<option value=\"$i\"> $i </option>";
  }
}

function printForm($courses,$term)
{
  // Generate the form for the courseResource planning

  print '<form  action="/planCourseResources" method="post">'."\n";
  print '<input type="hidden" name="term" value="'.$term.'" />';
  print '<tr>';
  print '<td align=center>';
  print '<input type="submit" value="submit" style="width:100%" />'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="number">'."\n";
  print "<option value=\"\">number ?</option>";
  foreach ($courses->list as $key => $course)
      print "<option value=\"$key\"> $key </option>";
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numAdmins">'."\n";
  printNum(-1,1);
  // setting a reasonable default (instead of -1)
  print "<option selected=\"selected\"> 0 </option>";
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numLecturers">'."\n";
  printNum(0,10);
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numRecitators">'."\n";
  printNum(0,10);
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numFullRecTas">'."\n";
  printNum(0,10);
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numHalfRecTas">'."\n";
  printNum(0,1);
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numFullUtilTas">'."\n";
  printNum(0,4);
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numHalfUtilTas">'."\n";
  printNum(0,1);
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numPartUtilTas">'."\n";
  printNum(0,6);
  print '    </select></td>'."\n";
  print '</tr>';
  print '</form>'."\n";
}

function printTermForm($term,$semesters)
{
  print '<div class="table-container">';
  print '<table>';
  print '<tr>';
  print '<form  action="/planCourseResources" method="post">'."\n";
  print '<td><input type="submit" value="Select term" />'."\n";
  print '</td>';
  print '<input type="hidden" name="term" value="'.$term.'" />';
  print '<td align=center><select class="type" name="term">'."\n";
  print "<option value=\"$term\">".$term."</option>";
  foreach ($semesters->list as $key => $semester)
    print "<option value=\"$key\"> $key </option>";
  print '    </select></td>'."\n";
  print '</td>';
  print '</form>'."\n";
  print '</tr>';
  print '</table>';
  print '<table>';
  print '<tr><td>';
  print '<form action="/planCourseResources" method="post">'."\n";
  print '<input type="hidden" name="term" value='.$term.'>'."\n";
  print '<input type="hidden" name="publish" value=YES>'."\n";
  print '<input type="submit" name="generateAssignments" value="Publish?"/>'."\n";
  print '</form>'."\n";
  print '</td></tr>';
  print '</table>';
  print '</div>';

}

function tryPublish($term,$semesters,$courseResources) {
  if (array_key_exists('publish',$_POST)) {
    print " Publishing the slots to the assignment tables: ".$term."\n";
    if (isset($semesters->list[$term]))
      $courseResources->registerAssignments();
  }
}

//==================================================================================================
// M A I N
//==================================================================================================

// get a full list of available semesters
$semesters = Semesters::fromDb();

// which term are we talking about?
$term = findTerm($semesters);

// get a full list of courses
$courses = Courses::fromDb();

// get list of CourseResources for the given semester (term)
$courseResources = CourseResources::fromDb($term);
$nEntries = sizeof($courseResources->list);

// publishing the slots?
tryPublish($term,$semesters,$courseResources);

$number = getPostVariable('number');
$numAdmins = getPostVariable('numAdmins');
$numLecturers = getPostVariable('numLecturers');
$numRecitators = getPostVariable('numRecitators');
$numFullRecTas = getPostVariable('numFullRecTas');
$numHalfRecTas = getPostVariable('numHalfRecTas');
$numFullUtilTas = getPostVariable('numFullUtilTas');
$numHalfUtilTas = getPostVariable('numHalfUtilTas');
$numPartUtilTas = getPostVariable('numPartUtilTas');

if (! isset($semesters->list[$term])) {
  print "<h1>ERROR</h1>\n";
  print "This term ($term) is not in our database. \n";
}
else {
  // initialize a new courseResource
  //print " Setting up resource $term:$number<br>\n";
  $courseResource = courseResource::fresh();
  $courseResource->term = $term;
  $courseResource->number = $number;
  $courseResource->numAdmins = $numAdmins;
  $courseResource->numLecturers = $numLecturers;
  $courseResource->numRecitators = $numRecitators;
  $courseResource->numFullRecTas = $numFullRecTas;
  $courseResource->numHalfRecTas = $numHalfRecTas;
  $courseResource->numFullUtilTas = $numFullUtilTas;
  $courseResource->numHalfUtilTas = $numHalfUtilTas;
  $courseResource->numPartUtilTas = $numPartUtilTas;
  
  if (isset($courses->list[$number])) {           // course must exist
    
    if (isset($courseResources->list[$number])) { // if course exists it will be overwritten
      if ($numAdmins == -1) {
        print "Remove from course resource in database: $term:$number<br>\n";
        $courseResource->removeFromDb();
      }
      else {
        print "Update course resource in database: $term:$number<br>\n";
        $courseResource->updateDb();
      }
    }
    else {
      // add it to the database
      if ($number === "") { // do not add empty selection
        print "Not adding empty course number.<br>\n";
      }
      else {
        print "Add new CourseResource in the database: $term:$number<br>\n";
        $courseResource->addToDb();
      }
    }
  
    // update the course resource list in memory
    $courseResources = CourseResources::fromDb($term);
    $nEntries =  sizeof($courseResources->list);
  }
}

// start the html page
print "<article class=\"page\">\n";
print "<h1>CourseResource List (Term: $term)</h1>\n";
print "<hr>\n";
printTermForm($term,$semesters);
print "<table>\n";

// loop through all course resources

$first = true;
foreach ($courseResources->list as $key => $courseResource) {
  if ($first) {
    $first = false;
    $courseResource->printTableHeader(false);
    printForm($courses,$term);
  }
  $courseResource->printTableRow(true);
}
if ($first) {
  $courseResource = CourseResource::fresh();
  $courseResource->printTableHeader(false);
  printForm($courses,$term);
}

print "</table>\n";
print "<p> &nbsp;&nbsp;&nbsp;&nbsp; $nEntries unique entries (for term: $term).".
      "<br> &nbsp;&nbsp;&nbsp;&nbsp;";
$courseResources->showSummary();
print "</p>\n";
print "<hr>\n";

// footer
print "<hr>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
