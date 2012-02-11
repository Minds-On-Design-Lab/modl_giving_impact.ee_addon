<?php
/*
    HOW TO USE sort_associative_array CLASS

    $this->load->library('MY_sort_associative_array');      
    $sorter = new MY_sort_associative_array;
    
    // some starting data
    $start_data = array(
    array('first_name' => 'John', 'last_name' => 'Smith', 'age' => 10),
    array('first_name' => 'Joe', 'last_name' => 'Smith', 'age' => 11),
    array('first_name' => 'Jake', 'last_name' => 'Xample', 'age' => 9),
    );

    // sort by last_name, then first_name
    print_r($sorter->sort_associative_array($start_data, 'last_name', 'first_name'));

    // sort by first_name, then last_name
    print_r($sorter->sort_associative_array($start_data, 'first_name', 'last_name'));

    // sort by last_name, then first_name (backwards)
    $sorter->backwards = true;
    print_r($sorter->sort_associative_array($start_data, 'last_name', 'first_name'));

    // sort numerically by age
    $sorter->numeric = true;
    print_r($sorter->sort_associative_array($start_data, 'age'));
*/
class MY_sort_associative_array {
  var $sort_fields;
  var $backwards = false;
  var $numeric = false;

  function sort_associative_array() {
    $args = func_get_args();
    $array = $args[0];
    if (!$array) return array();
    $this->sort_fields = array_slice($args, 1);
    if (!$this->sort_fields) return $array();

    if ($this->numeric) {
      usort($array, array($this, 'numericCompare'));
    } else {
      usort($array, array($this, 'stringCompare'));
    }
    return $array;
  }

  function numericCompare($a, $b) {
    foreach($this->sort_fields as $sort_field) {
      if ($a[$sort_field] == $b[$sort_field]) {
        continue;
      }
      return ($a[$sort_field] < $b[$sort_field]) ? ($this->backwards ? 1 : -1) : ($this->backwards ? -1 : 1);
    }
    return 0;
  }

  function stringCompare($a, $b) {
    foreach($this->sort_fields as $sort_field) {
      $cmp_result = strcasecmp($a[$sort_field], $b[$sort_field]);
      if ($cmp_result == 0) continue;
      return ($this->backwards ? -$cmp_result : $cmp_result);
    }
    return 0;
  }
}
?>