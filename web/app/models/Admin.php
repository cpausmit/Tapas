<?php

//+-------+--------------+------+-----+---------+-------+
//| Field | Type         | Null | Key | Default | Extra |
//+-------+--------------+------+-----+---------+-------+
//| Email | char(40)     | YES  | UNI | NULL    |       |
//| Level | mediumint(9) | YES  |     | 0       |       |
//+-------+--------------+------+-----+---------+-------+

include_once("app/models/Dbc.php");
include_once("app/models/Utils.php");

class Admins
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank admin
    $instance = new self();
    return $instance;
  }

  public static function fromDb()
  {
    // 'constructor' returns full list of admins
    $instance = new self();
    $adminRows = Dbc::getReader()->query("select * from Admins order by Email");
    foreach ($adminRows as $key => $row)
      $instance->addAdmin(Admin::fromRow($row));
    
    return $instance;
  }

  public function addAdmin($admin)
  {
    if (!isset($this->list[$admin->email]))
      $this->list[$admin->email] = $admin;
    else
      print " ERROR - trying to add an admin twice.<br>\n";
    
    return;
  }

  public function printTable()
  {
    if (sizeof($this->list) != 0) {
      print "<table>\n";
      $first = true;
      foreach ($this->list as $key => $admin) {
        if ($first)
          $admin->printTableHeader(false);
        $admin->printTableRow(false);
        $first = false;
      }
      print "\n</table>\n";
    }
    else
      print " Admin list is empty.<br>\n";
    
    return;
  }
}

class Admin
{
  // Property declaration
  public $email = '';
  public $level = -2;

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank admin
    $instance = new self();
    return $instance;
  }

  public static function fromEmail($email)
  {
    // 'constructor' with just email given
    $instance = new self();
    $sql = "select * from Admins where Email = '$email'";
    $admins = Dbc::getReader()->query($sql);
    foreach ($admins as $key => $row) {
      $instance->fill($row);
    }
 
    if ($instance->year == 0)
      $instance->email = $email;
 
    return $instance;
  }

  public static function fromRow(array $row)
  {
    // 'constructor' using db query row
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content

    $this->email = $row[0];
    $this->level = $row[1];
  }

  public function addToDb()
  {
    // adding the given admin instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      // check for duplicate
      $vals = sprintf("('%s',%d)",$this->email,$this->level);
      $sql = " insert into Admins values $vals";
      Dbc::getReader()->Exec($sql);
    }
    else
      print "<br> Invalid entry. STOP! ($sql)<br>";
  }

  public function updateDb()
  {
    // adding the given admin instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {

      // check for duplicate
      $vals = sprintf("Level = %d",$this->level);
      $sql = " update Admins set $vals where Email = '$this->email';";
      Dbc::getReader()->Exec($sql);
    }
    else
      print "<br> Invalid entry. STOP! ($sql)<br>";
  }
    
  protected function isValid()
  {
    // making sure admin information is valid

    $valid = true;

    // first check the email
    if (isEmail($this->email))
      print "";
    else
      return false;

    // now check the leel of access, no master level is possible
    if ($this->level > -2 and $this->level < 1)
      print "";
    else {
      print "Level is not allowed: $this->level.<br>\n";
      return false;
    }
   
    return $valid;
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relevant infromation

    print "<tr>\n";
    print "<td>&nbsp;" . $this->email . "&nbsp;</td>";
    print "<td>&nbsp;" . $this->level . "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Admin Email";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Level";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  public function isFresh()
  {
    // print one row of a table with the relevant infromation
    return ($this->level == -2);
  }

  public function printAdminForm($action)
  {
    print '<table>';
    print '<form  action="'.$action.'" method="post">'."\n";
    print '<tr><td>';
    print ' <b>FIXED EMAIL</b> ';
    print '</td><td>';
    print '  <select class="email" name="email">'."\n";
    print '  <option value="'.$this->email.'">'.$this->email.'</option>'."\n";
    print '  </select>'."\n";
    print '</td></tr>';
    print '<tr><td> ------ </td><td> </td></tr>';
    print '<tr><td>';
    print '  Level:&nbsp;'."\n";
    print '</td><td>';
    print '  <select style="width:100%;text-align-last:center" class="type" name="level">'."\n";
    print "  <option value=\"-1\"> -1 </option>";
    print "  <option value=\"0\">   0 </option>";
    print '  </select>'."\n";
    print '</td></tr>';
    print '<tr><td> ------ </td><td> </td></tr>';
    print '<tr><td></td><td>';
    // make sure to specify the type of action
    if ($this->isFresh())
      print '<input type="submit" value="submit new admin record" />'."\n";
    else
      print '<input type="submit" value="submit updated admin record" />'."\n";
    
    print '</td></tr>';
    print '</table>';
    print '</form>'."\n";
  }
}

?>
