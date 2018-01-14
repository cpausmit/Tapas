<?php
include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

function getLink()
{
  // the one place where we make access to the database and talk with mysql
  $pars = findAccessParameters();
  $host = $pars[0];
  $user = $pars[1];
  $passwd = $pars[2];

  $link = mysqli_connect($host,$user,$passwd,'Teaching')
    or die('Error ' . mysqli_error($link));
  
  return $link;
}

function showAssignment($link,$table,$option = "ALL")
{
  // show all assignments
  $query = "select * from $table order by Task";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=" . mysqli_errno($link) . ": " .
      mysqli_error($link) . "\n";
    exit();
  }
  $statement->bind_result($task,$person);
  print "<table>\n";
  print "<tr><th>&nbsp; Course &nbsp;</th><th> Type &nbsp;</th><th> Effort &nbsp;</th>";
  print "<th> TA type &nbsp;</th><th> Person &nbsp;</th><th> Id &nbsp;</th></tr>\n";
  $iF = 0;
  $iP = 0;
  while ($statement->fetch()) {
    $myTask = new TeachingTask($task);

    $display = 0;
    if ($option == "ALL")
      $display = 1;
    else if ($option == "TA" && $myTask->isTa() && $person != "" && $person != "EMPTY@mit.edu")
      $display = 1;
    else if ($option == "Unassigned" && ($person == "" || $person == "EMPTY@mit.edu"))
      $display = 1;

    if ($display) {
      print "<tr><td> "
        . "<a href=\"/showTaskSummary?number=" . $myTask->getCourse(). "\">"
        . $myTask->getCourse()
        . "</a>"
        . "&nbsp;</td><td>"
        . $myTask->getType()    . "&nbsp;</td><td>"
        . $myTask->getEffort()  . "&nbsp;</td><td>"
        . $myTask->getTaType()  . "&nbsp;</td><td>"
        . "<a href=\"/showTaSummary?email=" . $person . "\">"
        . $person
        . "</a>"
        . "&nbsp;</td><td>"
        . $myTask->generateId() . "&nbsp;</td></tr>\n";
      if ($myTask->isTa() && $myTask->getEffort() == 'full')
        $iF = $iF + 1;
      if ($myTask->isTa() && $myTask->getEffort() == 'half')
        $iF = $iF + 0.5;
      if ($myTask->isTa() && $myTask->getEffort() == 'part')
        $iP = $iP + 1;
    }   
  }
  print "</table>";
  print "<p> TA openings ($option): <b>$iF</b> (full time)  <b>$iP</b> (part time).</p><br> \n";
}

function showTas($link,$table)
{
  // show all assignments
  $query = "select Students.FirstName, Students.LastName, $table.Email, FullTime,PartTime"
    . " from $table,Students where Students.Email = $table.Email order by Students.LastName";
  //print " QUERY : $query";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=" . mysqli_errno($link) . ": " .
      mysqli_error($link) . "\n";
    exit();
  }
  $statement->bind_result($firstName,$lastName,$email,$fullTime,$partTime);
  print "<table>\n";
  print "<tr><th>&nbsp; Name &nbsp;</th><th>&nbsp; Email &nbsp;</th><th> FullTime &nbsp;</th><th> PartTime &nbsp;</th></tr>\n";
  $i = 0;
  $list = "";
  while ($statement->fetch()) {
    print "<tr><td>&nbsp;"
      . $lastName . ", " . $firstName
      . "&nbsp;</td><td>&nbsp;"
      . "<a href=\"/showTaSummary?email=" . $email . "\">" . $email . "</a>"
      . "&nbsp;</td><td>&nbsp;"
      . $fullTime
      . "&nbsp;</td><td>&nbsp;"
      . $partTime
      . "&nbsp;</td><td>&nbsp;"
      . "</tr>\n";
    $i = $i + 1;

    if ($list == "")
      $list = "$email";
    else
      $list = "$list,$email";
}
  print "</table>\n";
  print "<p> total identified active TAs: $i.</p> \n";

  print " ALL: $list<br>\n";
}

function findCourseNumbers($link,$table)
{
  // show all assignments
  $query = "select Students.FirstName, Students.LastName, $table.Person, $table.Task"
    . " from $table,Students where Students.Email = $table.Person order by $table.Task";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=" . mysqli_errno($link) . ": " .
      mysqli_error($link) . "\n";
    exit();
  }
  $statement->bind_result($firstName,$lastName,$email,$task);

  $i = 0;
  $courses = "";
  while ($statement->fetch()) {
    $myTask = new TeachingTask($task);
    $courses[$i] = $myTask->getCourse();
    $i = $i + 1;
  }

  return $courses;
}

function printTeachers($link,$table,$courses)
{
  // show all assignments
  $query = "select Person,Task from $table where Task like '%-Lec-%' order by Task";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=" . mysqli_errno($link) . ": " .
      mysqli_error($link) . "\n";
    exit();
  }
  $statement->bind_result($email,$task);
  $list = "";
  while ($statement->fetch()) {
    $myTask = new TeachingTask($task);
    $number = $myTask->getCourse();
    foreach ($courses as $key => $course) {
      if ($course == $number) {
        print " $course --> $email<br>\n";
        if ($list == "")
          $list = "$email";
        else
          $list = "$list,$email";
        break;
      }
    }
  }
  print " ALL: $list<br>\n";
  
  return;
}

function findActiveTable($link,$pattern)
{
  // find all active table matching the given pattern

  $i = 0;
  $activeTables = "";

  $query = "select TableName from ActiveTables where TableName like '%$pattern%'";
  //print ' ActiveTables: ' . $query;
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    print " ERROR - no active '%$pattern%' table: ErrNo=" . $errNum . ": " . $errMsg . "\n";
    exit();
  }

  $statement->bind_result($tableName);
  while ($statement->fetch()) {
    $activeTables[$i] = $tableName;
    $i = $i + 1;
  }
  return $activeTables;
}

function findLastTable($link,$pattern,$active)
{
  // find last table matching the pattern that is not active

  $lastTable = "";

  $query = "show tables like '%$pattern%'";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    print " ERROR - no active '%$pattern%' table: ErrNo=" . $errNum . ": " . $errMsg . "\n";
    exit();
  }
  $statement->bind_result($tableName);
  while ($statement->fetch()) {
    if ($tableName != $active)
      $lastTable = $tableName;
  }
  return $lastTable;
}

?>
