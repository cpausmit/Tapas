<?php

// mysql> describe Courses;
// +---------+----------+------+-----+---------+-------+
// | Field   | Type     | Null | Key | Default | Extra |
// +---------+----------+------+-----+---------+-------+
// | Number  | char(10) | YES  |     | NULL    |       | 
// | Name    | char(80) | YES  |     | NULL    |       | 
// | Version | int(11)  | YES  |     | NULL    |       | 
// +---------+----------+------+-----+---------+-------+
//
// TODO: make course number unique
//

class Course
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fromNumber($db,$number) // 'constructor' with just number given
  {
    $instance = new self();
    $rows = $db->exec("select * from Courses where Number = $number");
    $nRows = sizeof($rows);
    if ($nRows == 1)
      $instance->fill($rows);
    else
      print " ERROR -- loading failed (nRows: $nRows)\n";
    return $instance;
  }

  public static function fromRow(array $row)   // 'constructor' using db query row
  {
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content

    $this->number = $row[0];
    $this->name = $row[1];
    $this->version = intval($row[2]);
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relvant infromation

    print "<tr>\n";
    print "<td>&nbsp;";
    print "<a href=\"showTaskSummary?number=".$this->number."\">" . $this->number . "</a>";
    print "&nbsp;</td>";
    print "<td>&nbsp;";
    print $this->name;
    print "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Number";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Description";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  // Simple accessors
  public function getNumber() { return $this->number; }
  public function getName() { return $this->name; }
  public function getVersion() { return $this->version; }

  // Property declaration
  private $number = '';
  private $name = '';
  private $version = -1;

}

?>
