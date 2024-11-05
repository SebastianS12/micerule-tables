<?php

class ShowChallengeModel{
    //TODO: Missing Functions from ShowClassesModel.php 
    public $name;
    public $challengeSection;
    public $index;
    public $age;

    public function __construct($eventPostID, $challenge_name, $sectionName, $age)
    {
        $this->loadClassData($eventPostID, $challenge_name, $sectionName, $age);
    }

    private function loadClassData($eventPostID, $challenge_name, $sectionName, $age){
        global $wpdb;
        $location_id = EventProperties::getEventLocationID($eventPostID);
        $this->name = $challenge_name;
        $this->challengeSection = $sectionName;
        $this->index = $wpdb->get_var("SELECT challenge_index FROM ".$wpdb->prefix."micerule_show_challenges_indices 
                                            WHERE location_id = '".$location_id."' AND challenge_name = '".$challenge_name."' AND age = '".$age."'");
        $this->age = $age;
    }
}

class ChallengeIndexModel extends Model{
    public int $id;
    public $location_id;
    public $section;
    public $challenge_name;
    public $age;
    public $challenge_index;

    private function __construct($location_id, $section, $challenge_name, $age, $challenge_index)
    {
        $this->location_id = $location_id;
        $this->section = $section;
        $this->challenge_name = $challenge_name;
        $this->age = $age;
        $this->challenge_index = $challenge_index;
    }

    public static function create($location_id, $section, $challenge_name, $age, $challenge_index){
        $instance = new self($location_id, $section, $challenge_name, $age, $challenge_index);
        return $instance;
    }

    public static function createWithID($id, $location_id, $section, $challenge_name, $age, $challenge_index){
        $instance = self::create($location_id, $section, $challenge_name, $age, $challenge_index);
        $instance->id = $id;
        return $instance;
    }

    public function placements(): Collection
    {
        return $this->hasMany(ChallengePlacementModel::class, Table::CHALLENGE_PLACEMENTS, "index_id");
    }

    public function judgeSection(): ?JudgeSectionModel
    {
        return $this->hasOne(JudgeSectionModel::class, Table::JUDGES_SECTIONS, "section");
    }
}