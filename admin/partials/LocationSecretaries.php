<?php

class LocationSecretaries{
    public static function saveLocationSecretaryNames($locationPostID, $locationSecretaryNames){
        global $wpdb;
        $locationID = self::getLocationIDFromPostID($locationPostID);
        if($locationID != null){
            foreach($locationSecretaryNames as $secretaryPosition => $secretaryName){
                if($secretaryName != "")
                    $wpdb->replace($wpdb->prefix."micerule_location_secretaries", array("location_id" => $locationID, "secretary_position" => $secretaryPosition, "secretary_name" => $secretaryName));
                else
                    $wpdb->delete($wpdb->prefix."micerule_location_secretaries", array("location_id" => $locationID, "secretary_position" => $secretaryPosition));
            }
        }
    }

    public static function getLocationIDFromPostID($locationPostID){
        global $wpdb;
        return $wpdb->get_var("SELECT location_id FROM ".$wpdb->prefix."em_locations WHERE post_id = ".$locationPostID);
    }

    public static function getLocationSecretaryNames($locationID){
        global $wpdb;
        return $wpdb->get_col("SELECT secretary_name FROM ".$wpdb->prefix."micerule_location_secretaries WHERE location_id = ".$locationID);
    }
}