<?php

class ShowClassesModel{
    private $wpdb;
    private $showClassesTable;
    private $showClassesIndicesTable;
    private $showChallengesIndicesTable;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setDatabase();
    }

    private function setDatabase(){
        $this->showClassesTable = $this->wpdb->prefix."micerule_show_classes";
        $this->showClassesIndicesTable = $this->wpdb->prefix."micerule_show_classes_indices";
        $this->showChallengesIndicesTable = $this->wpdb->prefix."micerule_show_challenges_indices";
    }

    public function getNextSectionPosition($locationID, $section){
        $nextSectionPosition = $this->wpdb->get_var("SELECT section_position FROM ".$this->showClassesTable." WHERE location_id = ".$locationID." AND section = '".$section."' ORDER BY section_position DESC LIMIT 1");
        $nextSectionPosition = (isset($nextSectionPosition)) ? $nextSectionPosition + 1 : 0;

        return $nextSectionPosition;
    }

    public function updateClassIndex($locationID, $className, $age, $index){
        $this->wpdb->replace($this->showClassesIndicesTable, array("location_id" => $locationID, "class_name" => $className, "age" => $age, "class_index" => $index));
    }

    public function updateChallengeIndex($locationID, $challengeName, $age, $index){
        $this->wpdb->replace($this->showChallengesIndicesTable, array("location_id" => $locationID, "challenge_name" => $challengeName, "age" => $age, "challenge_index" => $index));
    }

    public function addShowClass($locationID, $className, $section, $sectionPosition){
        $this->wpdb->insert($this->showClassesTable, array("location_id" => $locationID, "class_name" => $className, "section" => $section, "section_position" => $sectionPosition));
    }

    public function getShowSectionClassesData($locationID, $section){
        //join class and indices table
        $adIndicesQuery = "SELECT location_id, class_name, class_index AS ad_index FROM ".$this->showClassesIndicesTable." WHERE age = 'Ad' AND location_id = ".$locationID;
        $u8IndicesQuery = "SELECT location_id, class_name, class_index AS u8_index FROM ".$this->showClassesIndicesTable." WHERE age = 'U8' AND location_id = ".$locationID;
        $indicesQuery = "(SELECT AD_I.location_id, ad_index, u8_index, AD_I.class_name FROM (".$adIndicesQuery.") AD_I LEFT JOIN (".$u8IndicesQuery.") U8_I ON AD_I.location_id = U8_I.location_id AND AD_I.class_name = U8_I.class_name)";
        return $this->wpdb->get_results("SELECT SC.class_name, SC.location_id, ad_index, u8_index, section_position FROM ".$this->showClassesTable. " SC LEFT JOIN ".$indicesQuery." CI ON SC.location_id = CI.location_id AND SC.class_name = CI.class_name WHERE SC.location_id = ".$locationID." AND section = '".$section."' ORDER BY section_position", ARRAY_A);
    }

    public function getShowOptionalClassesData($locationID){
        $indicesQuery = "SELECT location_id, class_name, class_index AS aa_index FROM ".$this->showClassesIndicesTable." WHERE age = 'AA' AND location_id = ".$locationID;
        return $this->wpdb->get_results("SELECT SC.class_name, SC.location_id, aa_index, section_position FROM ".$this->showClassesTable. " SC LEFT JOIN (".$indicesQuery.") CI ON SC.location_id = CI.location_id AND SC.class_name = CI.class_name WHERE SC.location_id = ".$locationID." AND section = 'optional' ORDER BY section_position", ARRAY_A);
    }

    public function getShowSectionClassNames($locationID, $section){
        return $this->wpdb->get_col("SELECT DISTINCT(CLASSES.class_name) FROM ".$this->showClassesTable." CLASSES INNER JOIN ".$this->showClassesIndicesTable." INDICES ON CLASSES.location_id = INDICES.location_id AND CLASSES.class_name = INDICES.class_name WHERE CLASSES.location_id = ".$locationID." AND section = '".$section."' ORDER BY class_index");
    }

    public function getClassIndex($locationID, $className, $age){
        return $this->wpdb->get_var("SELECT class_index FROM ".$this->showClassesIndicesTable." WHERE location_id = ".$locationID." AND class_name = '".$className."' AND age = '".$age."'");
    }

    public function getChallengeIndex($locationID, $challengeName, $age){
        return $this->wpdb->get_var("SELECT challenge_index FROM ".$this->showChallengesIndicesTable." WHERE location_id = ".$locationID." AND challenge_name = '".$challengeName."' AND age = '".$age."'");
    }

    public function swapClassIndices($locationID, $firstClassName, $secondClassName, $age){
        $swapQuery = "UPDATE ".$this->showClassesIndicesTable." SET class_index = (SELECT SUM(class_index) FROM (SELECT * FROM ".$this->showClassesIndicesTable.") AS INDICES WHERE INDICES.location_id = ".$locationID." AND INDICES.age = '".$age."' AND INDICES.class_name IN ('".$firstClassName."', '".$secondClassName."')) - class_index WHERE age = '".$age."' AND class_name IN ('".$firstClassName."', '".$secondClassName."')";
        $this->wpdb->query($this->wpdb->prepare($swapQuery));
    }

    public function swapSectionPosition($locationID, $firstClassName, $secondClassName){
        $swapQuery = "UPDATE ".$this->showClassesTable." SET section_position = (SELECT SUM(section_position) FROM (SELECT * FROM ".$this->showClassesTable.") AS CLASSES WHERE CLASSES.location_id = ".$locationID." AND CLASSES.class_name IN ('".$firstClassName."', '".$secondClassName."')) - section_position WHERE  class_name IN ('".$firstClassName."', '".$secondClassName."')";
        $this->wpdb->query($this->wpdb->prepare($swapQuery));
    }

    public function deleteClass($locationID, $className){
        $this->wpdb->delete($this->showClassesTable, array("location_id" => $locationID, "class_name" => $className));
    }
}
