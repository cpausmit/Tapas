<?php

include_once("app/models/Dbc.php");

class Tables
{

  // Declare a public constructor
  public function __construct($tableName)
  {
    $this->tableName = $tableName;
    $this->readTable();
  }

  public function __destruct() { }

  public function readTable() {
    // Reads the content of the database into memory
    // Note: the table name $this->tableName has to be set already
    $this->names = array();
    $rows = Dbc::getReader()->query("select TableName from ".$this->tableName);

    $i = 0;
    foreach ($rows as $key => $row) {
      $name = $row[0];
      $this->names[$i] = $name;
      $i = $i + 1;
    }
    $this->nNames = sizeof($this->names);
  }

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

  public function updateMatching($activity,$term)
  {
    $sql = " update $this->tableName set TableName='$activity$term' where TableName like '$activity%'";
    //print "<br> SQL: $sql <br>";
    Dbc::getReader()->Exec($sql);
    return;
  }

  // Simple accessors
  public function getTableName() { return $this->tableName; }
  public function getNames() { return $this->names; }
  public function getN() { return $this->nNames; }

  // Property declaration
  private $tableName = '';
  private $names = array();
  private $nNames = 0;
}

?>
