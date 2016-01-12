<?php

class Controller {

  protected $_jsFiles = array();
  protected $_jsFilesMap = array();
  protected $_cssFiles = array();
  protected $_cssFilesMap = array();
  protected $_debug = 0;

  // add a .js file to be loaded
  public function add_js($path) {
    if(!array_key_exists($path, $this->_jsFilesMap)) {
      array_push($this->_jsFiles, $path);
      $this->_jsFilesMap[$path] = 1;
    }
  }

  // Get the url for the min'd js
  public function get_min_js_url() {
    $url = "//" . $_SERVER['SERVER_NAME'] . "/min/?f=";
    for($i = 0; $i < count($this->_jsFiles); $i++) {
      if($i > 0)
        $url .= ",";
      $url .= $this->_jsFiles[$i];
    }
    if($this->_debug)
      $url .= "&debug";
    return $url;
  }

  // add a .css file to be loaded
  public function add_css($path) {
    if(!array_key_exists($path, $this->_cssFilesMap)) {
      array_push($this->_cssFiles, $path);
      $this->_cssFilesMap[$path] = 1;
    }
  }

  // Get the url for the min'd css
  public function get_min_css_url() {
    $url = "//" . $_SERVER['SERVER_NAME'] . "/min/?f=";
    for($i = 0; $i < count($this->_cssFiles); $i++) {
      if($i > 0)
        $url .= ",";
      $url .= $this->_cssFiles[$i];
    }
    if($this->_debug)
      $url .= "&debug";
    return $url;
  }
}

?>
