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

  public static function getEventMetaData($eventID){
    global $wpdb;
    $metaDataQueryResult = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."em_events WHERE post_id = '".$eventID."'",ARRAY_A);
    if(isset($metaDataQueryResult))
      return $metaDataQueryResult[0];
  }

  public static function getClassEditLink(int $eventPostID){
    global $wpdb;
    $classEditLink = "";
    $locationPostIDQueryResult = $wpdb->get_var("SELECT post_id FROM ".$wpdb->prefix."em_locations WHERE location_id = '".LocationHelper::getIDFromEventPostID($eventPostID)."'");
    if(isset($locationPostIDQueryResult))
      $classEditLink = get_permalink($locationPostIDQueryResult);

    return $classEditLink;
  }

  public static function getOppositeAge($age){
    return ($age == "Ad") ? "U8" : "Ad";
  }
}
