<?php

class Ta
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
    $this->fullTime = $row[1];
    $this->partTime = $row[2];
  }

  // Property declaration
  public $email = 'EMPTY@mit.edu';
  public $fullTime = 0;
  public $partTime = 0;

}

?>
