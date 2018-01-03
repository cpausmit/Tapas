<?php

// global variable to manage the result of the form input
$GLOBALS['COMPLETE'] = 2;

// make sure we are dealing with a administrator
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include("app/models/Dbc.php");
include("app/models/Semester.php");
include("app/models/Course.php");
include("app/models/CourseResource.php");

// Find the term we want to plan
function findTerm($semesters)
{
  $term = getPostVariable('term');
  $test = $GLOBALS['COMPLETE'];
  
  if ($test != 2) {
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
  else {
    if (! isset($semesters[$term])) {
      print "<article class=\"page\">\n";
      print "<h1>ERROR</h1>\n";
      print "<hr>\n";
      print "This term is not in our database: $term\n";
      // footer
      print "<hr>\n";
      print "<h2><a href=\"/planCourseResources\">try again</a></h2>\n";
      print '</article>'."\n";
      include("app/views/admin/footer.php");
      exit("");
    }
  }

  return $term;
}

// Get a post variable and indicate whether there was an access error
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

// Get all courses from the database
function getCoursesFromDb($db)
{
  // read complete courses table
  $courses = "";
  $rows = $db->query("select * from Courses order by Number");
  foreach ($rows as $key => $row) {
    $course = Course::fromRow($row);
    $courses[$course->number] = $course;
  }

  return $courses;
}

// Get all semesters from the database
function getSemestersFromDb($db)
{
  // read complete courses table
  $semesters = "";
  $rows = $db->query("select * from Semesters order by Term");
  foreach ($rows as $key => $row) {
    $semester = Semester::fromRow($row);
    $semesters[$semester->term] = $semester;
  }

  return $semesters;
}

// Get all course resources that are in the given term
function getCourseResourcesFromDb($db,$term)
{
  // do the query
  $rows = $db->query("select * from CourseResources where Term = '$term'");
  $courseResources = CourseResources::fresh();
  foreach ($rows as $key => $row) {
    $courseResource = CourseResource::fromRow($row);
    $courseResources->addCourseResource($courseResource);
  }

  return $courseResources;
}

// Generate the number option pannel from minimum to maximum
function printNum($min,$max)
{
  for ($i=$min; $i<=$max; $i++) {
    print "<option value=\"$i\"> $i </option>";
  }
}

function printGenerateAssignments($term)
{
  print '<table>';
  print '<form  action="/generateAssignments" method="post">'."\n";
  print '<input type="hidden" name="term" value="'.$term.'" />';
  print '<tr>';
  print '<td align=center><select class="type" name="action">'."\n";
  print "<option value=\"\"> action ?</option>";
  print "<option value=\"clear\"> clear  assignments </option>";
  print "<option value=\"generate\"> generate  assignments </option>";
  print '                 </select></td>'."\n";
  print '<td><input type="submit" value="select" />'."\n";
  print '</td></tr>';
  print '</table>';
  print '</form>'."\n";
}

// Generate the form for the courseResource planning
function printForm($courses,$term)
{
  print '<form  action="/planCourseResources" method="post">'."\n";
  print '<input type="hidden" name="term" value="'.$term.'" />';
  print '<tr>';
  print '<td align=center>';
  print '<input type="submit" value="submit" style="width:100%" />'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="number">'."\n";
  print "<option value=\"\">number ?</option>";
  foreach ($courses as $key => $course)
      print "<option value=\"$key\"> $key </option>";
  print '    </select></td>'."\n";
  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="numAdmins">'."\n";
  printNum(0,1);
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
//  print '<td align=center><select style="width:100%;text-align-last:center" class="type" name="term">'."\n";
//  print "<option value=\"$term\"></option>";
//  print '    </select></td>'."\n";
  print '</tr>';
  print '</form>'."\n";
}

function printTermForm($semesters)
{
  print '<table>';
  print '<form  action="/planCourseResources" method="post">'."\n";
  print '<tr>';
  print '<td>';
  print '  Term:&nbsp;'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="term">'."\n";
  print "<option value=\"\">term ?</option>";
  foreach ($semesters as $key => $semester)
    print "<option value=\"$key\"> $key </option>";
  print '    </select></td>'."\n";
  print '</td>';
  print '<td><input type="submit" value="select" />'."\n";
  print '</td></tr>';
  print '</table>';
  print '</form>'."\n";
}

function removeFromDb($db,$term,$number)
{
  // remove an existing student from the database
  $sql = " delete from CourseResources where Term = '$term' and Number = '$number'";
  $db->Exec($sql);
}

//==================================================================================================
// M A I N
//==================================================================================================

// connect to our database
$db = Dbc::getReader();

// get a full list of available semesters
$semesters = getSemestersFromDb($db);

// which term are we talking about?
$term = findTerm($semesters);

// get a full list of courses
$courses = getCoursesFromDb($db);

// get list of CourseResources for the given semester (term)
$courseResources = getCourseResourcesFromDb($db,$term);
$nEntries = sizeof($courseResources->list);

$number = getPostVariable('number');
$numAdmins = getPostVariable('numAdmins');
$numLecturers = getPostVariable('numLecturers');
$numRecitators = getPostVariable('numRecitators');
$numFullRecTas = getPostVariable('numFullRecTas');
$numHalfRecTas = getPostVariable('numHalfRecTas');
$numFullUtilTas = getPostVariable('numFullUtilTas');
$numHalfUtilTas = getPostVariable('numHalfUtilTas');
$numPartUtilTas = getPostVariable('numPartUtilTas');

if ($GLOBALS['COMPLETE']) {                 // All post variables are filled == ready to register
  if (! isset($semesters[$term])) {
    print "<h1>ERROR</h1>\n";
    print "This term is not in our database. \n";
  }
  else {
    if (isset($courseResources->list[$number])) { // if course exists it will be overwritten with the new info
      removeFromDb($db,$term,$number);
      print "Removed course $term:$number from our list<br>\n";
    }
    
    // initialize a new courseResource
    print " New resource $term:$number<br>\n";
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
    
    // add it to the database
    $courseResource->addToDb($db);
    print "Added new CourseResource to our list: $term:$number<br>\n";

    // update the TA list in memory
    $courseResources = getCourseResourcesFromDb($db,$term);
    $nEntries =  sizeof($courseResources->list);
  }
}

// start the html page
print "<article class=\"page\">\n";
print "<h1>Different Term?</h1>\n";
print "<hr>\n";
printTermForm($semesters);
print "<hr>\n";
print "<h1>CourseResource List (Term: $term)</h1>\n";
print "<hr>\n";
  
print "<table>\n";

// loop through all course resources
if ($courseResources->list != "") {
  $first = true;
  foreach ($courseResources->list as $key => $courseResource) {
    if ($first) {
      $first = false;
      $courseResource->printTableHeader(false);
      printForm($courses,$term);
    }
    $courseResource->printTableRow(true);
  }
  print "</table>\n";
  print "<p> &nbsp;&nbsp;&nbsp;&nbsp; $nEntries unique entries (for term: $term).".
        "<br> &nbsp;&nbsp;&nbsp;&nbsp;";
  $courseResources->showSummary();
  print "</p>\n";
  print "<hr>\n";
  // register all present assignment slots
  printGenerateAssignments($term);
}
else {
  $courseResource = courseResource::fresh();
  $courseResource->printTableHeader(false);
  printForm($courses,$term);
}

// footer
print "<hr>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
