<?php

include("app/views/ta/header.php");

// make sure we are dealing with a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

function testSelection($p1,$p2,$p3) {
  $valid = false;
  // test validity of selection
  if ($p1 != "" && $p2 != "" && $p3 != "" &&     // part 1: make sure preferences are all set
      $p1 != $p2 && $p2 != $p3 && $p1 != $p3) {  // part 2: they cannot be duplicated
    $valid = true;
  } 
  return($valid);
}

print '<article class="page">'."\n";
print '<h1>Selected TA Preferences</h1>';
print '<p>Your selected TA preferences are:</p>';
print ' ';

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

print '<p>';
$task1 = new TeachingTask($_POST['pref1']); print '&nbsp;&nbsp; 1: '; $task1->printTaTask();
$task2 = new TeachingTask($_POST['pref2']); print '&nbsp;&nbsp; 2: '; $task2->printTaTask();
$task3 = new TeachingTask($_POST['pref3']); print '&nbsp;&nbsp; 3: '; $task3->printTaTask();
print '</p>';

// test whether selection is valid
if (testSelection($_POST['pref1'],$_POST['pref2'],$_POST['pref3'])) {
  print '<p>Selection is valid. ';

  // connect to our database
  $link = getLink();

  // find the active tables
  $tableNames = findActiveTable($link,'Preferences');
  
  $query = "insert into $tableNames[0] (Email,Pref1,Pref2,Pref3) values ('"
    . $email . "','" . $_POST['pref1']. "','" . $_POST['pref2'] . "','" . $_POST['pref3'] . "')";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (!$rc) {
    $errNum = mysqli_errno($link);
    $errMsg = mysqli_error($link);
    if ($errNum = 1062) {
      $query = "update $tableNames[0] set Pref1='" . $_POST['pref1']
	. "',Pref2='" . $_POST['pref2'] . "',Pref3='" . $_POST['pref3']
	. "' where Email='" . $email ."'";
      print "Existing preferences have been updated.</p>";
      $statement = $link->prepare($query);
      $rc = $statement->execute();
      if (!$rc) {
	$errNum = mysqli_errno($link);
	$errMsg = mysqli_error($link);
	print " ERROR - could not register selection: ErrNo=" . $errNum . ": " . $errMsg . "\n";
	exit();
      }
    }
    else {
      print " ERROR - could not register selection: ErrNo=" . mysqli_errno($link) . ": " .
	mysqli_error($link) . "\n";
      exit();
    }
  }
  else {
    print 'Selection has been registered.</p>';
  }
  print '</select></td></tr>';
}
else {
  print '<p> Selection is NOT valid.<br>';
  print 'Please go back and make sure to select 1-3 distinct preferences.</p>';
}

print '</article>'."\n";

include("app/views/ta/footer.php");

?>
