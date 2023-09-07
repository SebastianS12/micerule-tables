<?php

class LabelModel{
    private $wpdb;
    private $showUserClassRegistrationsTable;
    private $showClassRegistrationsOrderTable;
    private $classIndicesTable;
    private $entriesTable;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setDatabaseTables();
    }

    private function setDatabaseTables(){
        $this->showUserClassRegistrationsTable = $this->wpdb->prefix."micerule_show_user_registrations";
        $this->showClassRegistrationsOrderTable = $this->wpdb->prefix."micerule_show_user_registrations_order";
        $this->classIndicesTable = $this->wpdb->prefix."micerule_show_classes_indices";
        $this->entriesTable = $this->wpdb->prefix."micerule_show_entries";
    }

    public function getLabelData($eventPostID){
        $labelData = array();
        $labelSQLData = $this->wpdb->get_results("SELECT user_name, class_index, absent, pen_number FROM ".$this->classIndicesTable." INDICES
                                        INNER JOIN ".$this->showUserClassRegistrationsTable." REGISTRATIONS ON INDICES.location_id = REGISTRATIONS.location_id AND REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age 
                                        INNER JOIN ".$this->showClassRegistrationsOrderTable." REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id
                                        INNER JOIN ".$this->entriesTable." ENTRIES ON REG_ORDER.class_registration_id = ENTRIES.class_registration_id AND REG_ORDER.registration_order = ENTRIES.registration_order
                                        WHERE event_post_id = ".$eventPostID." ORDER BY user_name, class_index, pen_number", ARRAY_A);

        foreach($labelSQLData as $userLabelData){
            if(!isset($labelData[$userLabelData['user_name']]))
                $labelData[$userLabelData['user_name']] = array();

            array_push($labelData[$userLabelData['user_name']], $userLabelData);
        }

        return $labelData;
    }


    
}