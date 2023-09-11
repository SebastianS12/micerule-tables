<?php

class ShowEntry{
    public $ID;
    public $penNumber;
    public $userName;
    public $className;
    public $sectionName;
    public $age;
    public $varietyName;
    public $absent;
    public $added;
    public $moved;

    private function __construct(){}

    public static function createWithPenNumber($eventPostID, $penNumber){
        $showEntry = new ShowEntry();
        $showEntry->penNumber = $penNumber;
        $entryData = self::getEntryDataWithPenNumber($eventPostID, $penNumber);
        $showEntry->loadEntryData($entryData);

        return $showEntry;
    }

    public static function createWithEntryID($entryID){
        $showEntry = new ShowEntry();
        $entryData = self::getEntryDatawithEntryID($entryID);
        $showEntry->loadEntryData($entryData);

        return $showEntry;
    }

    private static function getEntryDatawithPenNumber($eventPostID, $penNumber){
        global $wpdb;
        return $wpdb->get_row("SELECT ENTRIES.id, pen_number, variety_name, absent, moved, added, user_name, class_name, section, age FROM ".$wpdb->prefix."micerule_show_entries ENTRIES 
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id 
                               WHERE event_post_id = ".$eventPostID." AND pen_number = ".$penNumber, ARRAY_A);
    }

    private static function getEntryDatawithEntryID($entryID){
        global $wpdb;
        return $wpdb->get_row("SELECT ENTRIES.id, pen_number, variety_name, absent, moved, added, user_name, class_name, section, age FROM ".$wpdb->prefix."micerule_show_entries ENTRIES 
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id 
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id  
                               WHERE ENTRIES.id = ".$entryID, ARRAY_A);
    }

    private function loadEntryData($entryData){
        if($entryData != null){
            $this->ID = $entryData['id'];
            $this->penNumber = $entryData['pen_number'];
            $this->userName = $entryData['user_name'];
            $this->className = $entryData['class_name'];
            $this->sectionName = $entryData['section'];
            $this->age = $entryData['age'];
            $this->varietyName = $entryData['variety_name'];
            $this->absent = $entryData['absent'];
            $this->added = $entryData['added'];
            $this->moved = $entryData['moved'];
        } 
    }

    public function getRegistrationOrder(){
        global $wpdb;
        return $wpdb->get_var("SELECT registration_order FROM ".$wpdb->prefix."micerule_show_entries WHERE id = ".$this->ID);
    }

    public function save($classRegistrationID, $registrationOrder, $varietyName, $added, $moved){
        global $wpdb;
        if($this->ID == null)
            $wpdb->insert($wpdb->prefix."micerule_show_entries", array("class_registration_id" => $classRegistrationID, "registration_order" => $registrationOrder, "pen_number" => $this->penNumber, "variety_name" => $varietyName, "added" => $added, "moved" => $moved));
        else
            $wpdb->update($wpdb->prefix."micerule_show_entries", array("pen_number" => $this->penNumber, "variety_name" => $varietyName), array("id" => $this->ID));
    }

    public function delete(){
        global $wpdb;
        $wpdb->delete($wpdb->prefix."micerule_show_entries", array("id" => $this->ID));
    }

    public function editAbsent($isAbsent){
        global $wpdb;
        $wpdb->update($wpdb->prefix."micerule_show_entries", array("absent" => $isAbsent), array("id" => $this->ID));
    }

    public function editVarietyName($varietyName){
        global $wpdb;
        $wpdb->update($wpdb->prefix."micerule_show_entries", array("variety_name" => $varietyName), array("id" => $this->ID));
    }
}