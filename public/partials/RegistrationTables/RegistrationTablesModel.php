<?php

class RegistrationTablesModel{
    private $wpdb;
    private $showUserClassRegistrationsTable;
    private $showClassRegistrationsOrderTable;
    private $showUserJuniorRegistrationsTable;
    private $showClassesTable;
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
        $this->showUserJuniorRegistrationsTable = $this->wpdb->prefix."micerule_show_user_junior_registrations";
        $this->showClassesTable = $this->wpdb->prefix."micerule_show_classes";
        $this->classIndicesTable = $this->wpdb->prefix."micerule_show_classes_indices";
        $this->penNumberTable = $this->wpdb->prefix."micerule_show_pen_numbers";
    }

    private function addUserClassRegistration($eventPostID, $userName, $locationID, $className, $age){
        $classRegistrationID = self::getClassRegistrationID($eventPostID, $userName, $className, $age);
        if(!isset($classRegistrationID))
            $this->wpdb->insert($this->showUserClassRegistrationsTable, array("event_post_id" => $eventPostID, "user_name" => $userName, "location_id" => $locationID, "class_name" => $className, "age" => $age));

        return $this->wpdb->insert_id;
    }

    private function deleteUserRegistrationEntry($eventPostID, $userName, $className, $age){
        $classRegistrationID = self::getClassRegistrationID($eventPostID, $userName, $className, $age);
        $this->wpdb->delete($this->showUserClassRegistrationsTable, array("class_registration_id" => $classRegistrationID));
    }

    private function getClassRegistrationID($eventPostID, $userName, $className, $age){
        return $this->wpdb->get_var("SELECT class_registration_id FROM ".$this->showUserClassRegistrationsTable. " WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."' AND class_name = '".$className."' AND age = '".$age."'");   
    }

    public function addUserRegistration($eventPostID, $userName, $locationID, $className, $age, $isJuniorMember){
        $classRegistrationID = self::getClassRegistrationID($eventPostID, $userName, $className, $age);
        if(!isset($classRegistrationID))
            $classRegistrationID = self::addUserClassRegistration($eventPostID, $userName, $locationID, $className, $age);

        $registrationOrder = self::getNextClassRegistrationOrder($eventPostID, $className, $age);
        $insertStatus = $this->wpdb->insert($this->showClassRegistrationsOrderTable, array("class_registration_id" => $classRegistrationID, "registration_order" => $registrationOrder));
        $showOptionsModel = new ShowOptionsModel();
        if($isJuniorMember && $insertStatus != 0 && $showOptionsModel->getShowOptions($locationID)['allow_junior'])
            self::addJuniorRegistration($classRegistrationID, $registrationOrder);
    }

    private function addJuniorRegistration($classRegistrationID, $registrationOrder){
        $this->wpdb->insert($this->showUserJuniorRegistrationsTable, array("class_registration_id" => $classRegistrationID, "registration_order" => $registrationOrder));
    }

    private function getNextClassRegistrationOrder($eventPostID, $className, $age){
        $currentHighestRegistrationOrder = $this->wpdb->get_var("SELECT registration_order FROM ".$this->showClassRegistrationsOrderTable." ORDER_TABLE INNER JOIN (SELECT class_registration_id FROM ".$this->showUserClassRegistrationsTable." WHERE event_post_id = ".$eventPostID." AND class_name = '".$className."' AND age = '".$age."') CLASS ON ORDER_TABLE.class_registration_id = CLASS.class_registration_id ORDER BY registration_order DESC LIMIT 1");
        $nextClassRegistrationOrder = 0;
        if(isset($currentHighestRegistrationOrder))
            $nextClassRegistrationOrder = $currentHighestRegistrationOrder + 1;

        return $nextClassRegistrationOrder;
    }

    public function deleteUserRegistration($eventPostID, $userName, $className, $age){
        $classRegistrationID = self::getClassRegistrationID($eventPostID, $userName, $className, $age);
        if(isset($classRegistrationID)){
            $this->wpdb->delete($this->showClassRegistrationsOrderTable, array("class_registration_id" => $classRegistrationID, "registration_order" => self::getUserHighestClassRegistrationOrder($classRegistrationID)));

            if(self::getUserClassRegistrationCount($eventPostID, $userName, $className, $age) == 0)
                self::deleteUserRegistrationEntry($eventPostID, $userName, $className, $age);
        }
    }

    private function getUserHighestClassRegistrationOrder($classRegistrationID){
        return $this->wpdb->get_var("SELECT registration_order FROM ".$this->showClassRegistrationsOrderTable." WHERE class_registration_id = ".$classRegistrationID." ORDER BY registration_order DESC LIMIT 1");
    }

    public function getUserClassRegistrationCount($eventPostID, $userName, $className, $age){
        $classRegistrationID = self::getClassRegistrationID($eventPostID, $userName, $className, $age);
        $userClassRegistrationCount = 0;
        if(isset($classRegistrationID))
            $userClassRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable." WHERE class_registration_id = ".$classRegistrationID);

        return $userClassRegistrationCount;
    }

    public function getClassRegistrationCount($eventPostID, $className, $age){
        $classRegistrationCount = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->showClassRegistrationsOrderTable. " ORDER_TABLE INNER JOIN (SELECT class_registration_id FROM ".$this->showUserClassRegistrationsTable." WHERE event_post_id = ".$eventPostID." AND class_name = '".$className."' AND age = '".$age."') CLASS ON ORDER_TABLE.class_registration_id = CLASS.class_registration_id");
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
                                         LEFT JOIN ".$this->showUserJuniorRegistrationsTable." JUNIOR_REGISTRATIONS ON REGISTRATIONS.class_registration_id = JUNIOR_REGISTRATIONS.class_registration_id 
                                         INNER JOIN ".$this->classIndicesTable." INDICES ON REGISTRATIONS.location_id = INDICES.location_id AND ((REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age) OR (INDICES.class_name = 'Junior') AND JUNIOR_REGISTRATIONS.class_registration_id IS NOT NULL) 
                                         WHERE REGISTRATIONS.event_post_id = ".$eventPostID." AND REGISTRATIONS.user_name = '".$userName."' GROUP BY INDICES.class_name, age ORDER BY class_index", ARRAY_A);
    }

    public function getClassRegistrations($eventPostID, $className){
        return $this->wpdb->get_results("SELECT INDICES.class_name, class_index, REGISTRATIONS.age AS age, REGISTRATIONS.class_registration_id, registration_order 
        FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS  INNER JOIN ".$this->showClassRegistrationsOrderTable." REGISTRATIONS_ORDER ON REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id 
        INNER JOIN ".$this->classIndicesTable." INDICES ON REGISTRATIONS.location_id = INDICES.location_id AND (REGISTRATIONS.class_name = INDICES.class_name AND REGISTRATIONS.age = INDICES.age) 
        WHERE REGISTRATIONS.event_post_id = ".$eventPostID." AND INDICES.class_name != 'Junior' AND INDICES.class_name = '".$className."' ORDER BY class_index", ARRAY_A);
    }

    public function getFancierNames($eventPostID){
        return $this->wpdb->get_col("SELECT DISTINCT user_name FROM sm1_micerule_show_user_registrations WHERE event_post_id = ".$eventPostID."");
    }

    public function getFancierRegistrationCount($eventPostID, $userName){
        return $this->wpdb->get_var("SELECT COUNT(*) AS registration_count FROM ".$this->showUserClassRegistrationsTable." REGISTRATIONS INNER JOIN ".$this->showClassRegistrationsOrderTable." REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."'");
    }

    //TODO: PennumberModel
    public function savePenNumber($classRegistrationID, $registrationOrder, $penNumber){
        $penNumberID = $this->getPenNumberID($classRegistrationID, $registrationOrder);
        if($penNumberID == null)
            $this->wpdb->insert($this->penNumberTable, array("class_registration_id" => $classRegistrationID, "registration_order" => $registrationOrder, "pen_number" => $penNumber));
        else
            $this->wpdb->update($this->penNumberTable, array("pen_number" => $penNumber), array("id" => $penNumberID));
    }

    private function getPenNumberID($classRegistrationID, $registrationOrder){
        return $this->wpdb->get_var("SELECT id FROM ".$this->penNumberTable." WHERE class_registration_id = ".$classRegistrationID." AND registration_order = ".$registrationOrder);
    }
}