<?php

include("app/views/ta/header.php");

// make sure we are dealing with a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include("app/models/Dbc.php");
include("app/models/Course.php");
include("app/models/Tables.php");

function testSelection($p1,$p2,$p3) {
  $valid = false;
  // test validity of selection
  if ($p1 != "" && $p2 != "" && $p3 != "" &&     // part 1: make sure preferences are all set
      $p1 != $p2 && $p2 != $p3 && $p1 != $p3) {  // part 2: they cannot be duplicated
    $valid = true;
  } 
  return($valid);
}

// database pointer
$db = Dbc::getReader();
$courses = Courses::fromDb($db);

// get all TAs and the possible full time assignments
$planningTables = new Tables($db,'PlanningTables');
$preferencesTable = $planningTables->getUniqueMatchingName('Preferences');

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

print '<article class="page">'."\n";
print '<h1>Selected TA Preferences</h1>';
print '<p>Your selected TA preferences are:</p>';
print ' ';

print '<p>';
$task1 = new TeachingTask($_POST['pref1']); print '&nbsp;&nbsp; 1: '; $task1->printTaTask();
$task2 = new TeachingTask($_POST['pref2']); print '&nbsp;&nbsp; 2: '; $task2->printTaTask();
$task3 = new TeachingTask($_POST['pref3']); print '&nbsp;&nbsp; 3: '; $task3->printTaTask();
print '</p>';

// test whether selection is valid
if (testSelection($_POST['pref1'],$_POST['pref2'],$_POST['pref3'])) {
  print '<p>Selection is valid. ';

  $sql = "insert into $preferencesTable (Email,Pref1,Pref2,Pref3) values ('"
         . $email . "','" . $_POST['pref1']. "','" . $_POST['pref2']
         . "','" . $_POST['pref3'] . "')";
  $rc = $db->Exec($sql);
  $errorArray = $db->errorInfo();
  if (!$rc) {
    if ($errorArray[0] == 23000 && $errorArray[1] == 1062) {
      print "<!-- WARNING -- duplicate entry.<br> --> \n";
      $sql = "update $preferencesTable set Pref1='" . $_POST['pref1']
           . "',Pref2='" . $_POST['pref2'] . "',Pref3='" . $_POST['pref3']
           . "' where Email='" . $email ."'";
      $rc = $db->Exec($sql);
      if (!$rc) {
        print "<br>\n ERROR -- PDO::errorInfo():\n";
        print_r($db->errorInfo());
        exit();
      }
      else
        print "Existing preferences have been updated.</p>";
    }
    else if ($errorArray[0] == 42 && $errorArray[1] == 1146) {
      print "<br>\n ERROR - table ($preferencesTable) does not exist.\n";
    }
    else {
      print "<br>\n ERROR -- PDO::errorInfo():\n";
      print_r($db->errorInfo());
    }
  }
  else
    print 'Selection has been registered.</p>';

  print '</select></td></tr>';
}
else {
  print '<p> Selection is NOT valid.<br>';
  print 'Please go back and make sure to select 1-3 distinct preferences.</p>';
}

print '</article>'."\n";

include("app/views/ta/footer.php");

?>
