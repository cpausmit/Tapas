<?php

// mysql> describe PreferencesF2015;
//+-------+----------+------+-----+---------+-------+
//| Field | Type     | Null | Key | Default | Extra |
//+-------+----------+------+-----+---------+-------+
//| Term  | char(5)  | YES  | MUL | NULL    |       |
//| Email | char(40) | YES  |     | NULL    |       |
//| Pref1 | text     | YES  |     | NULL    |       |
//| Pref2 | text     | YES  |     | NULL    |       |
//| Pref3 | text     | YES  |     | NULL    |       |
//+-------+----------+------+-----+---------+-------+
// alter table Preferences add constraint onePerTerm unique(Term, Email);

include_once("app/models/Dbc.php");
include_once("app/models/Utils.php");

class Preferences
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank preference
    $instance = new self();
    return $instance;
  }

  public static function fromDb($term)
  {
    // 'constructor' returns full list of preferences
    $instance = new self();
    $sql = "select * from Preferences where Term='$term' order by Email";
    $preferenceRows = Dbc::getReader()->query($sql);
    foreach ($preferenceRows as $key => $row)
      $instance->add(Preference::fromRow($row));
    
    return $instance;
  }

  public function add($preference)
  {
    if (!isset($this->list[$preference->email]))
      $this->list[$preference->email] = $preference;
    else
      print " ERROR - trying to add a preference twice.<br>\n";
    
    return;
  }
}

class Preference
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fromRow(array $row)   // 'constructor' using db query row
  {
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content

    $this->term = $row[0];
    $this->email = $row[1];
    $this->pref1 = $row[2];
    $this->pref2 = $row[3];
    $this->pref3 = $row[4];
  }

  protected function isValid()
  {
    // making sure preferences information is valid

    $valid = true;
    if (isEmail($this->email))
      print "";
    else
      return false;
   
    return $valid;
  }

  public function addToDb()
  {
    // adding the given preference to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      $vals = sprintf("('%s','%s','%s','%s','%s')",
                      $this->term,$this->email,$this->pref1,$this->pref2,$this->pref3);
      $sql = " insert into Preferences values $vals";
      //print "<br> SQL: $sql <br>";
      Dbc::getReader()->Exec($sql);
    }
    else
      print '<br> Invalid entry. STOP!<br>';
  }

  public function updateDb()
  {
    // updating the given preference to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      $form = "Pref1 = '%s', Pref2 = '%s', Pref3 = '%s'";
      $vals = sprintf($form,$this->pref1,$this->pref2,$this->pref3);
      $sql = " update Preferences set $vals where Term = '$this->term' and Email = '$this->email';";
      Dbc::getReader()->Exec($sql);
    }
    else
      print '<br> Invalid entry. STOP!<br>';
  }

  public function deleteFromDb()
  {
    // delete the given preference to the database

    // make sure this is a valid new entry
    $sql = "delete from Preferences where Term = '$this->term' and Email = '$this->email';";
    Dbc::getReader()->Exec($sql);
  }

  // Property declaration
  public $term = '';
  public $email = '';
  public $pref1 = 0;
  public $pref2 = 0;
  public $pref3 = 0;

}

?>
