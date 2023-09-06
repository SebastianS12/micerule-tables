<?php

class NextPenNumber{
    public static function getNextPenNumber($locationID, $className, $age){
        global $wpdb;
        return $wpdb->get_var("SELECT next_pen_number FROM ".$wpdb->prefix."micerule_show_classes_next_pen_numbers
                               WHERE location_id = ".$locationID." AND class_name = '".$className."' AND age = '".$age."'");
    }
    //TODO: Single Truth for table names
    public static function saveNextPenNumber($locationID, $className, $age, $nextPenNumber){
        global $wpdb;
        $wpdb->replace($wpdb->prefix."micerule_show_classes_next_pen_numbers", array("location_id" => $locationID, "class_name" => $className, "age" => $age, "next_pen_number" => $nextPenNumber));
    }
}