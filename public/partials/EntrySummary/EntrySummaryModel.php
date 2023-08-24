<?php

class EntrySummaryModel{
    private $wpdb;
    private $showUserClassRegistrationsTable;
    private $showClassRegistrationsOrderTable;
    private $classIndicesTable;
    private $penNumberTable;

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
        $this->penNumberTable = $this->wpdb->prefix."micerule_show_pen_numbers";
    }

    public function getFancierEntries($eventPostID, $userName){
        return $this->wpdb->get_results("SELECT INDICES.class_name, INDICES.class_index, REGISTRATIONS.age, user_name, pen_number FROM sm1_micerule_show_classes_indices INDICES 
                                         INNER JOIN sm1_micerule_show_user_registrations REGISTRATIONS ON REGISTRATIONS.location_id = INDICES.location_id AND REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age 
                                         INNER JOIN sm1_micerule_show_user_registrations_order REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id 
                                         INNER JOIN sm1_micerule_show_pen_numbers PENNUMBERS ON REG_ORDER.class_registration_id = PENNUMBERS.class_registration_id AND REG_ORDER.registration_order = PENNUMBERS.registration_order 
                                         WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."' ORDER BY class_index, REG_ORDER.registration_order", ARRAY_A);
    }
}