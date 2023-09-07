<?php

class EntrySummaryModel{
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

    public function getFancierEntries($eventPostID, $userName){
        return $this->wpdb->get_results("SELECT INDICES.class_name, INDICES.class_index, REGISTRATIONS.age, user_name, pen_number FROM ".$this->classIndicesTable." INDICES 
                                         INNER JOIN ".$this->showUserClassRegistrationsTable." REGISTRATIONS ON REGISTRATIONS.location_id = INDICES.location_id AND REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age 
                                         INNER JOIN ".$this->showClassRegistrationsOrderTable." REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id 
                                         INNER JOIN ".$this->entriesTable." ENTRIES ON REG_ORDER.class_registration_id = ENTRIES.class_registration_id AND REG_ORDER.registration_order = ENTRIES.registration_order 
                                         WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."' ORDER BY class_index, pen_number", ARRAY_A);
    }

    public function getAllAbsentCheckValue($eventPostID, $userName){
        return $this->wpdb->get_var("SELECT case when COUNT(absent) = 0 then TRUE else FALSE end as is_absent FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS
                                     INNER JOIN ".$this->showClassRegistrationsOrderTable." REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id 
                                     INNER JOIN ".$this->entriesTable." ENTRIES ON REG_ORDER.class_registration_id = ENTRIES.class_registration_id AND REG_ORDER.registration_order = ENTRIES.registration_order 
                                     WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."' AND absent = FALSE");
    }
}