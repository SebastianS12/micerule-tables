<?php 

class ShowOptionsModel{
    private $wpdb;
    private $showOptionTable;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setDatabaseTables();
    }

    private function setDatabaseTables(){
        $this->showOptionTable = $this->wpdb->prefix."micerule_show_options";
    }

    public function getShowOptions($locationID){
        return $this->wpdb->get_row("SELECT * FROM ".$this->showOptionTable." WHERE location_id = ".$locationID, ARRAY_A);
    }

    public function saveShowOptions($showOptions){
        $this->wpdb->replace($this->showOptionTable, $showOptions);
    }
}