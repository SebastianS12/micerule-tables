<?php

class UserClassRegistration{
    public $registrationID;
    public $locationID;
    public $eventPostID;
    public $userName;
    public $className;
    public $age;

    public function __construct($eventPostID, $userName, $className, $age)
    {
        $this->loadUserRegistrationData($eventPostID, $userName, $className, $age);
    }

    private function loadUserRegistrationData($eventPostID, $userName, $className, $age){
        global $wpdb;
        $this->eventPostID = $eventPostID;
        $this->locationID = EventProperties::getEventLocationID($eventPostID);
        $this->userName = $userName;
        $this->className = $className;
        $this->age = $age;
        $this->registrationID = $wpdb->get_var("SELECT class_registration_id FROM ".$wpdb->prefix."micerule_show_user_registrations 
                                                WHERE event_post_id = ".$eventPostID." AND user_name = '".$userName."' AND class_name = '".$className."' AND age = '".$age."'");
    }

    public function addUserRegistration(){
        global $wpdb;
        if(!isset($this->registrationID))
            $this->addUserClassRegistration();

        $registrationOrder = $this->getNextRegistrationOrder();
        $wpdb->insert($wpdb->prefix."micerule_show_user_registrations_order", array("class_registration_id" => $this->registrationID, "registration_order" => $registrationOrder));
        $showOptionsModel = new ShowOptionsModel();
        if(EventUser::isJuniorMember($this->userName) && $showOptionsModel->getShowOptions($this->locationID)['allow_junior'])
            $this->addJuniorRegistration($this->registrationID, $registrationOrder);
    }

    private function addUserClassRegistration(){
        global $wpdb;
        if(!isset($this->registrationID)){
            $wpdb->insert($wpdb->prefix."micerule_show_user_registrations", array("event_post_id" => $this->eventPostID, "user_name" => $this->userName, "location_id" => $this->locationID, "class_name" => $this->className, "age" => $this->age));
            $this->registrationID = $wpdb->insert_id;
        }   
    }

    private function addJuniorRegistration($classRegistrationID, $registrationOrder){
        global $wpdb;
        $wpdb->insert($wpdb->prefix."micerule_show_user_junior_registrations", array("class_registration_id" => $classRegistrationID, "registration_order" => $registrationOrder));
    }

    private function getNextRegistrationOrder(){
        $highestRegistrationOrder = $this->getHighestRegistrationOrder();
        $nextRegistrationOrder = 0;
        if($highestRegistrationOrder != null)
            $nextRegistrationOrder = $highestRegistrationOrder + 1;

        return $nextRegistrationOrder;
    }

    public function getHighestRegistrationOrder(){
        global $wpdb;
        return $wpdb->get_var("SELECT registration_order FROM ".$wpdb->prefix."micerule_show_user_registrations_order REG_ORDER
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON REG_ORDER.class_registration_id = REGISTRATIONS.class_registration_id
                               WHERE event_post_id = ".$this->eventPostID." AND class_name = '".$this->className."' AND age = '".$this->age."' ORDER BY registration_order DESC LIMIT 1");
    }

    public function deleteUserRegistration($registrationOrder){
        global $wpdb;
        if(isset($this->registrationID)){
            $wpdb->delete($wpdb->prefix."micerule_show_user_registrations_order", array("class_registration_id" => $this->registrationID, "registration_order" => $registrationOrder));

            if($this->getUserClassRegistrationCount() == 0)
                $this->deleteUserClassRegistration();
        }
    }

    private function deleteUserClassRegistration(){
        global $wpdb;
        $wpdb->delete($wpdb->prefix."micerule_show_user_registrations", array("class_registration_id" => $this->registrationID));
    }

    public function getUserHighestClassRegistrationOrder(){
        global $wpdb;
        return $wpdb->get_var("SELECT registration_order FROM ".$wpdb->prefix."micerule_show_user_registrations_order WHERE class_registration_id = ".$this->registrationID." ORDER BY registration_order DESC LIMIT 1");
    }

    public function getUserClassRegistrationCount(){
        global $wpdb;
        $userClassRegistrationCount = 0;
        if(isset($this->registrationID))
            $userClassRegistrationCount = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."micerule_show_user_registrations_order WHERE class_registration_id = ".$this->registrationID);

        return $userClassRegistrationCount;
    }
}