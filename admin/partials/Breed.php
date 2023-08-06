<?php

class Breed {
    //TODO: move add/delete/update breed functions

    public static function getBreedData($varietyName){
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_breeds WHERE name = '".$varietyName."'", ARRAY_A);
    }
}