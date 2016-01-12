<?php

class ActiveTables
{

  // Declare a public constructor
  public function __construct($db)
  {
    $i = 0;
    $rows = $db->query("select TableName from ActiveTables");
    foreach ($rows as $key => $row) {
      $name = $row[0];
      $this->names[$i] = $name;
      $i = $i + 1;
    }
    $this->nNames = sizeof($this->names);
  }

  public function __destruct() { }

  public function getMatchingNames($pattern)
  {
    $i = 0;
    $matchingNames = "";
    foreach ($this->names as $key => $name) {
      if (preg_match("/$pattern/",$name)) {
	$matchingNames[$i] = $name;
	$i = $i + 1;
      }
    }
    return $matchingNames;
  }

  public function getUniqueMatchingName($pattern)
  {
    $name = "";
    $names = $this->getMatchingNames($pattern);
    $nMatches = sizeof($names);
    if ($nMatches == 1)
      $name = $names[0];
    else
      print " ERROR -- no unique active table found (nTables: $nMatches)<br>\n";

    return $name;
  }

  // Simple accessors
  public function getActiveNames() { return $this->names; }
  public function getN() { return $this->nNames; }

  // Property declaration
  private $names = '';
  private $nNames = 0;
}

?>
