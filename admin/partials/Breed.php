<?php

class Breed {
    //TODO: move add/delete/update breed functions

    public static function getBreedData($varietyName){
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_breeds WHERE name = '".$varietyName."'", ARRAY_A);
    }

    public static function getBreedIconUrls(){
        global $wpdb;
        return $wpdb->get_results("SELECT DISTINCT icon_url FROM ".$wpdb->prefix."micerule_breeds", ARRAY_A);
    }

    public static function getSectionBreedNames($section){
        global $wpdb;
        return $wpdb->get_col("SELECT name FROM ".$wpdb->prefix."micerule_breeds WHERE section = '".$section."'");
    }
}