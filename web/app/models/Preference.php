<?php

// mysql> describe PreferencesF2015;
// +-------+----------+------+-----+---------+-------+
// | Field | Type     | Null | Key | Default | Extra |
// +-------+----------+------+-----+---------+-------+
// | Email | char(20) | NO   | PRI |         |       | 
// | Pref1 | text     | YES  |     | NULL    |       | 
// | Pref2 | text     | YES  |     | NULL    |       | 
// | Pref3 | text     | YES  |     | NULL    |       | 
// +-------+----------+------+-----+---------+-------+

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

  public static function fromDb($db,$term)
  {
    // 'constructor' returns full list of preferences
    $instance = new self();
    $preferenceRows = $db->query("select * from Preferences$term order by Email");
    foreach ($preferenceRows as $key => $row)
      $instance->addPreference(Preference::fromRow($row));
    
    return $instance;
  }

  public function addPreference($preference)
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

    $this->email = $row[0];
    $this->pref1 = $row[1];
    $this->pref2 = $row[2];
    $this->pref3 = $row[3];
  }

  // Property declaration
  public $email = '';
  public $pref1 = 0;
  public $pref2 = 0;
  public $pref3 = 0;

}

?>
