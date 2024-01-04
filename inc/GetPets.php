<?php 

class GetPets {
  function __construct() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'pets';
    // $ourQuery = $wpdb->prepare("SELECT * FROM $tablename WHERE species = %s LIMIT 100", array($_GET['species']));

    // $ourQuery = $wpdb->prepare("SELECT * FROM $tablename LIMIT 100");
    // $ourQuery = $wpdb->prepare("SELECT * FROM wp_pets WHERE species = %s AND birthyear > %d LIMIT 10", array('hamster', 2018));
    // $pets = $wpdb->get_results($ourQuery);

    // $this->pets = $wpdb->get_results($ourQuery);

    /*  Make it dynamic */
    $this->args = $this->getArgs();
    $this->placeholders = $this->createPlaceholders();

    $query = "SELECT * FROM $tablename ";
    $countQuery = "SELECT COUNT(*) FROM $tablename ";
    $query .= $this->createWhereText();
    $countQuery .= $this->createWhereText();
    $query .= " LIMIT 100";

    $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->placeholders));
    $this->pets = $wpdb->get_results($wpdb->prepare( $query, $this->placeholders ));
    
    // var_dump($pets);
  } // __construct 

   
  function getArgs() {
    $temp = array(
      'favcolor' => sanitize_text_field($_GET['favcolor']),
      'species' => sanitize_text_field($_GET['species']),
      'minyear' => sanitize_text_field($_GET['minyear']),
      'maxyear' => sanitize_text_field($_GET['maxyear']),
      'minweight' => sanitize_text_field($_GET['minweight']),
      'maxweight' => sanitize_text_field($_GET['maxweight']),
      'favhobby' => sanitize_text_field($_GET['favhobby']),
      'favfood' => sanitize_text_field($_GET['favfood']),
    );

    return array_filter($temp, function($x){
      return $x;
    });
  } // getArgs()
   
  

  /*  updated */
  /*
  function getArgs() {
    $temp = [];
 
    if (isset($_GET['favcolor'])) $temp['favcolor'] = sanitize_text_field($_GET['favcolor']);
    if (isset($_GET['species'])) $temp['species'] = sanitize_text_field($_GET['species']);
    if (isset($_GET['minyear'])) $temp['minyear'] = sanitize_text_field($_GET['minyear']);
    if (isset($_GET['maxyear'])) $temp['maxyear'] = sanitize_text_field($_GET['maxyear']);
    if (isset($_GET['minweight'])) $temp['minweight'] = sanitize_text_field($_GET['minweight']);
    if (isset($_GET['maxweight'])) $temp['maxweight'] = sanitize_text_field($_GET['maxweight']);
    if (isset($_GET['favhobby'])) $temp['favhobby'] = sanitize_text_field($_GET['favhobby']);
    if (isset($_GET['favfood'])) $temp['favfood'] = sanitize_text_field($_GET['favfood']);
 
    return $temp;
 
  }
  */

  function createPlaceholders() {
    return array_map(function($x) {
      return $x;
    }, $this->args);
  } // createPlaceholders()

  function createWhereText() {
    $whereQuery = "";

    if(count($this->args)) {
      $whereQuery = "WHERE ";
    }

    $currentPosition = 0;
    foreach($this->args as $index => $item) {
      $whereQuery .= $this->specificQuery($index);
      if($currentPosition != count($this->args) -1) {
        $whereQuery .= " AND ";
      }
      $currentPosition++;
    }

    return $whereQuery;
  } // createWhereText()

  function specificQuery($index) {
    switch($index) {
      case "minweight":
        return "petweight >= %d";
      case "maxweight":
        return "petweight <= %d";
      case "minyear":
        return "birthyear >= %d";
      case "maxyear":
        return "birthyear <= %d";
      default:
        return $index . " = %s";
    }
  } // specificQuery()

} // class GetPets {}

  