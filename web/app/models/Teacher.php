<?php

// mysql> describe Faculties;
// +-----------+----------+------+-----+---------+-------+
// | Field     | Type     | Null | Key | Default | Extra |
// +-----------+----------+------+-----+---------+-------+
// | FirstName | char(20) | YES  |     | NULL    |       | 
// | LastName  | char(20) | YES  |     | NULL    |       | 
// | Email     | char(40) | YES  | UNI | NULL    |       | 
// | Position  | char(20) | YES  |     | NULL    |       | 
// | Status    | char(20) | YES  |     | NULL    |       | 
// +-----------+----------+------+-----+---------+-------+

class Teacher
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fromEmail($db,$email) // 'constructor' with just email given
  {
    $instance = new self();
    $rows = $db->exec("select * from Faculties where Email = '$email'");
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

    $this->firstName = $row[0];
    $this->lastName = $row[1];
    $this->email = $row[2];
    $this->position = $row[3];
    $this->status = $row[4];
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relvant infromation

    print "<tr>\n";
    print "<td>&nbsp;";
    print $this->lastName . ', ' . $this->firstName;
    print "&nbsp;</td>";
    print "<td>&nbsp;";
    print "<a href=\"showTaSummary?email=" . $this->email . "\">" . $this->email . "</a>";
    print "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Teachers Name";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Email";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  // Simple accessors
  public function getFirstName() { return $this->firstName; }
  public function getLastName() { return $this->lastName; }
  public function getEmail() { return $this->email; }
  public function getPosition() { return $this->position; }
  public function getStatus() { return $this->status; }

  // Property declaration
  public $firstName = '';
  public $lastName = '';
  public $email = 'EMPTY@mit.edu';
  public $position = '';
  public $status = '';

}

?>
