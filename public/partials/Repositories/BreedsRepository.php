<?php

class BreedsRepository{
    public function getSectionBreedNames(string $section): array|null{
        global $wpdb;
        return $wpdb->get_col("SELECT name FROM ".$wpdb->prefix."micerule_breeds WHERE section = '".$section."'");
    }
}