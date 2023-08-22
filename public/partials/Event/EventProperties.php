<?php

/**
* Class that contains Name Information such as Section Names
*
*/

class EventProperties {
  const SECTIONNAMES = array('SELFS', 'TANS', 'MARKED', 'SATINS', 'AOVS');
  const CHALLENGENAMES = array('selfs' => 'SELF CHALLENGE', 'tans' => 'TAN CHALLENGE', 'marked' => 'MARKED CHALLENGE', 'satins' => 'SATIN CHALLENGE', 'aovs' => 'AOV CHALLENGE');
  const GRANDCHALLENGE = "GRAND CHALLENGE";
  const AGESECTIONS = array("Ad", "U8");

  public static function getChallengeName($sectionName){
    return EventProperties::CHALLENGENAMES[$sectionName];
  }


  public static function getEventLocationID($eventPostID){
    global $wpdb;
    $locationID = 0;
    $locationIDQueryResult = $wpdb->get_var("SELECT location_id FROM ".$wpdb->prefix."em_events WHERE post_id = '".$eventPostID."'");
    if(isset($locationIDQueryResult))
      $locationID = $locationIDQueryResult;

    return $locationID;
  }

  public static function saveEventDeadline($eventPostID, $eventDeadlineString){
    global $wpdb;
    $wpdb->replace($wpdb->prefix."micerule_event_deadline", array("event_post_id" => $eventPostID, "event_deadline" => strtotime($eventDeadlineString)));
  }

  public static function getEventDeadline($eventPostID){
    global $wpdb;
    return $wpdb->get_var("SELECT event_deadline FROM ".$wpdb->prefix."micerule_event_deadline WHERE event_post_id = ".$eventPostID);
  }

  public static function getEventMetaData($eventID){
    global $wpdb;
    $metaDataQueryResult = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."em_events WHERE post_id = '".$eventID."'",ARRAY_A);
    if(isset($metaDataQueryResult))
      return $metaDataQueryResult[0];
  }

  public static function getLocationSecretaries($locationID){
    global $wpdb;
    $locationSecretaryPostID = 0;
    $secretaryPostIDQueryResult = $wpdb->get_var("SELECT post_id FROM ".$wpdb->prefix."em_locations WHERE location_id = '".$locationID."'");
    if(isset($secretaryPostIDQueryResult))
      $locationSecretaryPostID = $secretaryPostIDQueryResult;

    $locationSecretaries = get_post_meta($locationSecretaryPostID, 'micerule_data_location_secretaries', true);
    if($locationSecretaries == ""){
      $locationSecretaries = array('name'=>array());
    }

    return $locationSecretaries;
  }


  public static function getClassEditLink($eventID){
    global $wpdb;
    $classEditLink = "";
    $locationPostIDQueryResult = $wpdb->get_var("SELECT post_id FROM ".$wpdb->prefix."em_locations WHERE location_id = '".self::getEventLocationID($eventID)."'");
    if(isset($locationPostIDQueryResult))
      $classEditLink = get_permalink($locationPostIDQueryResult);

    return $classEditLink;
  }
}
