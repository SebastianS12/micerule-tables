<?php

class LocationHelper{
    public static function getIDFromLocationPostID(int $locationPostID){
        global $wpdb;
        return $wpdb->get_var("SELECT location_id FROM ".$wpdb->prefix."em_locations WHERE post_id = ".$locationPostID);
    }

    public static function getIDFromEventPostID(int $eventPostID){
        global $wpdb;
        $locationID = 0;
        $locationIDQueryResult = $wpdb->get_var("SELECT location_id FROM ".$wpdb->prefix."em_events WHERE post_id = '".$eventPostID."'");
        if(isset($locationIDQueryResult))
          $locationID = $locationIDQueryResult;
    
        return $locationID;
      }
}