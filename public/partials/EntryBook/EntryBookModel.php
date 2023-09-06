<?php

class EntryBookModel{
    private $wpdb;
    private $classPlacementsTable;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setDatabaseTables();
    }

    private function setDatabaseTables(){
        $this->classPlacementsTable = $this->wpdb->prefix."micerule_show_class_placements";
    }

    
}