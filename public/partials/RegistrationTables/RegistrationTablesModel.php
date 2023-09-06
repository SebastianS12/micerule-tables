<?php

class RegistrationTablesModel{
    private $wpdb;
    private $showUserClassRegistrationsTable;
    private $showClassRegistrationsOrderTable;
    private $showUserJuniorRegistrationsTable;
    private $showClassesTable;
    private $classIndicesTable;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setDatabaseTables();
    }

    private function setDatabaseTables(){
        $this->showUserClassRegistrationsTable = $this->wpdb->prefix."micerule_show_user_registrations";
        $this->showClassRegistrationsOrderTable = $this->wpdb->prefix."micerule_show_user_registrations_order";
        $this->showUserJuniorRegistrationsTable = $this->wpdb->prefix."micerule_show_user_junior_registrations";
        $this->showClassesTable = $this->wpdb->prefix."micerule_show_classes";
        $this->classIndicesTable = $this->wpdb->prefix."micerule_show_classes_indices";
    }

    public function getClassRegistrationCount($eventPostID, $className, $age){
        $classRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable. " REG_ORDER INNER JOIN ".$this->showUserClassRegistrationsTable." REGISTRATIONS ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id WHERE event_post_id = ".$eventPostID." AND class_name = '".$className."' AND age = '".$age."'");
        if($classRegistrationCount == null)
            $classRegistrationCount = 0;
        
        return $classRegistrationCount;
    }

    public function getSectionRegistrationCount($eventPostID, $locationID, $sectionName, $age){
        $sectionRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable." ORDER_TABLE INNER JOIN ".$this->showUserClassRegistrationsTable." CLASS ON ORDER_TABLE.class_registration_id = CLASS.class_registration_id INNER JOIN ".$this->showClassesTable." SHOW_CLASSES ON CLASS.class_name = SHOW_CLASSES.class_name WHERE event_post_id = ".$eventPostID." AND SHOW_CLASSES.location_id = ".$locationID." AND section = '".$sectionName."' AND age = '".$age."'");
        if($sectionRegistrationCount == null)
            $sectionRegistrationCount = 0;

        return $sectionRegistrationCount;
    }

    public function getGrandChallengeRegistrationCount($eventPostID, $locationID, $age){
        $grandChallengeRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable." ORDER_TABLE INNER JOIN ".$this->showUserClassRegistrationsTable." CLASS ON ORDER_TABLE.class_registration_id = CLASS.class_registration_id INNER JOIN ".$this->showClassesTable." SHOW_CLASSES ON CLASS.class_name = SHOW_CLASSES.class_name WHERE event_post_id = ".$eventPostID." AND SHOW_CLASSES.location_id = ".$locationID." AND age = '".$age."'");
        if($grandChallengeRegistrationCount == null)
            $grandChallengeRegistrationCount = 0;

        return $grandChallengeRegistrationCount;
    }

    public function getUserRegistrations($eventPostID, $userName){
        return $this->wpdb->get_results("SELECT INDICES.class_name, class_index, CASE WHEN INDICES.class_name = 'Junior' THEN  'AA' ELSE REGISTRATIONS.age END AS age, COUNT(REGISTRATIONS.class_name) AS registration_count
                                         FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS  INNER JOIN ".$this->showClassRegistrationsOrderTable." REGISTRATIONS_ORDER on REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
                                         LEFT JOIN ".$this->showUserJuniorRegistrationsTable." JUNIOR_REGISTRATIONS ON REGISTRATIONS_ORDER.class_registration_id = JUNIOR_REGISTRATIONS.class_registration_id AND REGISTRATIONS_ORDER.registration_order = JUNIOR_REGISTRATIONS.registration_order
                                         INNER JOIN ".$this->classIndicesTable." INDICES ON REGISTRATIONS.location_id = INDICES.location_id AND ((REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age) OR (INDICES.class_name = 'Junior') AND JUNIOR_REGISTRATIONS.class_registration_id IS NOT NULL) 
                                         WHERE REGISTRATIONS.event_post_id = ".$eventPostID." AND REGISTRATIONS.user_name = '".$userName."' GROUP BY INDICES.class_name, age ORDER BY class_index", ARRAY_A);
    }

    public function getClassRegistrations($eventPostID, $className){
        return $this->wpdb->get_results("SELECT INDICES.class_name, class_index, REGISTRATIONS.age AS age, REGISTRATIONS.class_registration_id, registration_order 
        FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS  INNER JOIN ".$this->showClassRegistrationsOrderTable." REGISTRATIONS_ORDER ON REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id 
        INNER JOIN ".$this->classIndicesTable." INDICES ON REGISTRATIONS.location_id = INDICES.location_id AND (REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age) 
        WHERE REGISTRATIONS.event_post_id = ".$eventPostID." AND INDICES.class_name != 'Junior' AND INDICES.class_name = '".$className."' ORDER BY class_index, REGISTRATIONS_ORDER.registration_order", ARRAY_A);
    }

    public function getFancierNames($eventPostID){
        return $this->wpdb->get_col("SELECT DISTINCT user_name FROM sm1_micerule_show_user_registrations WHERE event_post_id = ".$eventPostID."");
    }

    public function getFancierRegistrationCount($eventPostID, $userName){
        return $this->wpdb->get_var("SELECT COUNT(*) AS registration_count FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS INNER JOIN ".$this->showClassRegistrationsOrderTable." REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."'");
    }
}