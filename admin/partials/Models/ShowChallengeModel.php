<?php

class ShowChallengeModel{
    //TODO: Missing Functions from ShowClassesModel.php 
    public $name;
    public $challengeSection;
    public $index;
    public $age;

    public function __construct($eventPostID, $challengeName, $sectionName, $age)
    {
        $this->loadClassData($eventPostID, $challengeName, $sectionName, $age);
    }

    private function loadClassData($eventPostID, $challengeName, $sectionName, $age){
        global $wpdb;
        $locationID = EventProperties::getEventLocationID($eventPostID);
        $this->name = $challengeName;
        $this->challengeSection = $sectionName;
        $this->index = $wpdb->get_var("SELECT challenge_index FROM ".$wpdb->prefix."micerule_show_challenges_indices 
                                            WHERE location_id = '".$locationID."' AND challenge_name = '".$challengeName."' AND age = '".$age."'");
        $this->age = $age;
    }
}

class ChallengeIndexModel{
    public $id;
    public $locationID;
    public $section;
    public $challengeName;
    public $age;
    public $challengeIndex;
    private $indexTable;

    private function __construct($locationID, $section, $challengeName, $age, $challengeIndex)
    {
        $this->locationID = $locationID;
        $this->section = $section;
        $this->challengeName = $challengeName;
        $this->age = $age;
        $this->challengeIndex = $challengeIndex;

        global $wpdb;
        $this->indexTable = $wpdb->prefix."micerule_show_challenges_indices";
    }

    public static function create($locationID, $section, $challengeName, $age, $challengeIndex){
        $instance = new self($locationID, $section, $challengeName, $age, $challengeIndex);
        return $instance;
    }

    public static function createWithID($id, $locationID, $section, $challengeName, $age, $challengeIndex){
        $instance = self::create($locationID, $section, $challengeName, $age, $challengeIndex);
        $instance->id = $id;
        return $instance;
    }

    public function save(){
        global $wpdb;
        if($this->id){
            $wpdb->update($this->indexTable, $this->getValues(), array('id' => $this->id));
        }else{
            $wpdb->insert($this->indexTable, $this->getValues());
        }
    }

    private function getValues(){
        return array('location_id' => $this->locationID, 'section' => $this->section, 'challenge_name' => $this->challengeName, 'age' => $this->age, 'challenge_index' => $this->challengeIndex);
    }

    public function delete(){
        global $wpdb;
        $wpdb->delete($this->indexTable, array('id' => $this->id));
    }
}