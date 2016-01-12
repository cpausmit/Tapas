<?php

// load database and models
include("app/models/Dbc.php");
include("app/models/Student.php");

$handle = fopen('/etc/my.cnf','r');
$on = 0;
$user = "";
$pass = "";
while(!feof($handle)) {
  $tmp = fgets($handle);
  $tmp = substr_replace($tmp,"",-1);
  //echo " INPUT - >>" . $tmp . "<<\n";

  if ($tmp == "[mysql-teaching]") {
    $on = 1;
  }
  if ($on == 1 && $user == "" && $pass == "") {
    echo "SEEN " . $tmp . "\n";
    if (strpos($tmp,'user=') !== false) {
      $a = explode('=',$tmp);
      $user = $a[1];
    }
    if (strpos($tmp,'password=') !== false) {
      $a = explode('=',$tmp);
      $pass = $a[1];
    }
  }
}
$status = fclose($handle);

// create an instance
$db = Dbc::getReader();
$students = $db->query("select * from Students order by lastName");

// print basic page
print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Graduate Student TA Listing</h1>\n";
print "<hr>\n";
print "<table>\n";

// loop
$first = true;
foreach ($students as $key => $row) {
  $student = Student::fromRow($row);
  $student->printTableRow($first);
  $first = false;
}

// footer
print "</table>\n";
print "<hr>\n";
print "</article>\n";

?>
