<?php

class ShowChallengeModel{
    //TODO: Missing Functions from ShowClassesModel.php 
    public $challengeName;
    public $challengeSection;
    public $challengeIndex;
    public $age;

    public function __construct($eventPostID, $challengeName, $sectionName, $age)
    {
        $this->loadClassData($eventPostID, $challengeName, $sectionName, $age);
    }

    private function loadClassData($eventPostID, $challengeName, $sectionName, $age){
        global $wpdb;
        $locationID = EventProperties::getEventLocationID($eventPostID);
        $this->challengeName = $challengeName;
        $this->challengeSection = $sectionName;
        $this->challengeIndex = $wpdb->get_var("SELECT challenge_index FROM ".$wpdb->prefix."micerule_show_challenges_indices 
                                            WHERE location_id = '".$locationID."' AND challenge_name = '".$challengeName."' AND age = '".$age."'");
        $this->age = $age;
    }
}