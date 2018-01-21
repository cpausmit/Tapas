<?php

include_once("app/models/Dbc.php");

class Tables
{

  // Declare a public constructor
  public function __construct($tableName)
  {
    $this->tableName = $tableName;
    $i = 0;
    $rows = Dbc::getReader()->query("select TableName from ".$tableName);
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
      print " ERROR -- no unique table found in $name (nTables: $nMatches)<br>\n";

    return $name;
  }

  // Simple accessors
  public function getTableName() { return $this->tableName; }
  public function getNames() { return $this->names; }
  public function getN() { return $this->nNames; }

  // Property declaration
  private $tableName = '';
  private $names = '';
  private $nNames = 0;
}

?>
