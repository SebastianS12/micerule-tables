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
        $classRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable. " REG_ORDER 
                                                        INNER JOIN ".$this->showUserClassRegistrationsTable." REGISTRATIONS ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id
                                                        INNER JOIN ".$this->showClassesTable." CLASSES ON REGISTRATIONS.class_id = CLASSES.id 
                                                        WHERE event_post_id = ".$eventPostID." AND class_name = '".$className."' AND age = '".$age."'");
        if($classRegistrationCount == null)
            $classRegistrationCount = 0;
        
        return $classRegistrationCount;
    }

    public function getSectionRegistrationCount($eventPostID, $sectionName, $age){
        $sectionRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable." ORDER_TABLE 
                                                          INNER JOIN ".$this->showUserClassRegistrationsTable." CLASS ON ORDER_TABLE.class_registration_id = CLASS.class_registration_id 
                                                          INNER JOIN ".$this->showClassesTable." SHOW_CLASSES ON CLASS.class_id = SHOW_CLASSES.id 
                                                          WHERE event_post_id = ".$eventPostID." AND section = '".$sectionName."' AND age = '".$age."'");
        if($sectionRegistrationCount == null)
            $sectionRegistrationCount = 0;

        return $sectionRegistrationCount;
    }

    public function getGrandChallengeRegistrationCount($eventPostID, $age){
        $grandChallengeRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable." ORDER_TABLE 
                                                                 INNER JOIN ".$this->showUserClassRegistrationsTable." CLASS ON ORDER_TABLE.class_registration_id = CLASS.class_registration_id 
                                                                 INNER JOIN ".$this->showClassesTable." SHOW_CLASSES ON CLASS.class_id = SHOW_CLASSES.id 
                                                                 WHERE event_post_id = ".$eventPostID." AND age = '".$age."'");
        if($grandChallengeRegistrationCount == null)
            $grandChallengeRegistrationCount = 0;

        return $grandChallengeRegistrationCount;
    }

    public function getUserRegistrations($eventPostID, $userName){
        global $wpdb;
        return $this->wpdb->get_results("SELECT class_name, class_index, age, registration_count FROM (
            SELECT event_post_id, user_name, class_name, class_index, REGISTRATIONS.age, COUNT(class_name) AS registration_count
            FROM sm1_micerule_show_user_registrations REGISTRATIONS  
            INNER JOIN sm1_micerule_show_user_registrations_order REGISTRATIONS_ORDER on REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
            INNER JOIN sm1_micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
            INNER JOIN sm1_micerule_show_classes_indices INDICES ON CLASSES.id = INDICES.class_id AND INDICES.age = REGISTRATIONS.age 
            WHERE event_post_id = ".$eventPostID."  AND user_name = '".$userName."' GROUP BY class_name, age 
            UNION  
            SELECT event_post_id, user_name, class_name, class_index, REGISTRATIONS.age, COUNT(class_name) AS registration_count
            FROM sm1_micerule_show_user_registrations REGISTRATIONS  
            INNER JOIN sm1_micerule_show_user_registrations_order REGISTRATIONS_ORDER on REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
            INNER JOIN sm1_micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
            INNER JOIN sm1_micerule_show_classes_indices INDICES ON CLASSES.id = INDICES.class_id AND INDICES.age = REGISTRATIONS.age 
            WHERE event_post_id = ".$eventPostID."  AND user_name = '".$userName."' GROUP BY class_name, age) USER_REGISTRATIONS  ORDER BY class_index", ARRAY_A);
    }

    public function getClassRegistrations($eventPostID, $className){
        return $this->wpdb->get_results("SELECT class_name, class_index, REGISTRATIONS.age AS age, REGISTRATIONS.class_registration_id, registration_order 
        FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS  INNER JOIN ".$this->showClassRegistrationsOrderTable." REGISTRATIONS_ORDER ON REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
        INNER JOIN ".$this->showClassesTable." CLASSES ON REGISTRATIONS.class_id = CLASSES.id
        INNER JOIN ".$this->classIndicesTable." INDICES ON REGISTRATIONS.class_id = INDICES.class_id AND REGISTRATIONS.age = INDICES.age
        WHERE REGISTRATIONS.event_post_id = ".$eventPostID." AND class_name != 'Junior' AND class_name = '".$className."' ORDER BY class_index, REGISTRATIONS_ORDER.registration_order", ARRAY_A);
    }

    public function getFancierNames($eventPostID){
        return $this->wpdb->get_col("SELECT DISTINCT user_name FROM sm1_micerule_show_user_registrations WHERE event_post_id = ".$eventPostID."");
    }

    public function getFancierRegistrationCount($eventPostID, $userName){
        return $this->wpdb->get_var("SELECT COUNT(*) AS registration_count FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS INNER JOIN ".$this->showClassRegistrationsOrderTable." REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."'");
    }
}