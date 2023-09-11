<?php

class NextPenNumber{
    public static function getNextPenNumber($locationID, $className, $age){
        global $wpdb;
        $classIndexID = self::getClassIndexID($locationID, $className, $age);
        return $wpdb->get_var("SELECT next_pen_number FROM ".$wpdb->prefix."micerule_show_classes_next_pen_numbers
                               WHERE class_index_id = ".$classIndexID);
    }
    //TODO: Single Truth for table names
    public static function saveNextPenNumber($locationID, $className, $age, $nextPenNumber){
        global $wpdb;
        $classIndexID = self::getClassIndexID($locationID, $className, $age);
        $wpdb->replace($wpdb->prefix."micerule_show_classes_next_pen_numbers", array("class_index_id" => $classIndexID, "next_pen_number" => $nextPenNumber));
    }

    private static function getClassIndexID($locationID, $className, $age){
        global $wpdb;
        return $wpdb->get_var("SELECT INDICES.id FROM ".$wpdb->prefix."micerule_show_classes_indices INDICES
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                               WHERE location_id = ".$locationID." AND class_name = '".$className."' AND age = '".$age."'");
    }
}