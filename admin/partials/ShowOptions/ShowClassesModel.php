<?php
//TODO: Class does too much
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
        $classID = $this->getClassID($locationID, $className);
        $classIndexID = $this->getClassIndexID($classID, $age);
        if($classIndexID != null)
            $this->wpdb->update($this->showClassesIndicesTable, array("class_index" => $index), array("id" => $classIndexID));
        else
            $this->wpdb->insert($this->showClassesIndicesTable, array("class_id" => $classID, "age" => $age, "class_index" => $index));
    }

    public function updateChallengeIndex($locationID, $section, $challengeName, $age, $index){
        $challengeIndexID = $this->getChallengeIndexID($locationID, $challengeName, $age);
        if($challengeIndexID != null)
            $this->wpdb->update($this->showChallengesIndicesTable, array("challenge_index" => $index), array("id" => $challengeIndexID));
        else
            $this->wpdb->insert($this->showChallengesIndicesTable, array("location_id" => $locationID, "section" => $section, "challenge_name" => $challengeName, "age" => $age, "challenge_index" => $index));
    }

    public function addShowClass($locationID, $className, $section, $sectionPosition){
        $this->wpdb->insert($this->showClassesTable, array("location_id" => $locationID, "class_name" => $className, "section" => $section, "section_position" => $sectionPosition));
    }

    private function getClassID($locationID, $className){
        return $this->wpdb->get_var("SELECT id FROM ".$this->showClassesTable." WHERE location_id = ".$locationID." AND class_name = '".$className."'");
    }

    private function getClassIndexID($classID, $age){
        return $this->wpdb->get_var("SELECT id FROM ".$this->showClassesIndicesTable." WHERE class_id = ".$classID." AND age = '".$age."'");
    }

    private function getChallengeIndexID($locationID, $challengeName, $age){
        return $this->wpdb->get_var("SELECT id FROM ".$this->showChallengesIndicesTable." WHERE location_id = ".$locationID." AND challenge_name = '".$challengeName."' AND age = '".$age."'");
    }

    public function getShowSectionClassesData($locationID, $section){
        //join class and indices table
        $adIndicesQuery = "SELECT class_id, class_index AS ad_index FROM ".$this->showClassesIndicesTable." WHERE age = 'Ad'";
        $u8IndicesQuery = "SELECT class_id, class_index AS u8_index FROM ".$this->showClassesIndicesTable." WHERE age = 'U8'";
        $indicesQuery = "(SELECT AD_I.class_id, ad_index, u8_index FROM (".$adIndicesQuery.") AD_I LEFT JOIN (".$u8IndicesQuery.") U8_I ON AD_I.class_id = U8_I.class_id)";
        return $this->wpdb->get_results("SELECT SC.id as class_id, SC.class_name, SC.location_id, ad_index, u8_index, section_position FROM ".$this->showClassesTable. " SC LEFT JOIN ".$indicesQuery." CI ON SC.id = CI.class_id WHERE SC.location_id = ".$locationID." AND section = '".$section."' ORDER BY section_position", ARRAY_A);
    }

    public function getShowOptionalClassesData($locationID){
        $indicesQuery = "SELECT class_id, class_index AS aa_index FROM ".$this->showClassesIndicesTable." WHERE age = 'AA'";
        return $this->wpdb->get_results("SELECT SC.class_name, SC.location_id, aa_index, section_position FROM ".$this->showClassesTable. " SC LEFT JOIN (".$indicesQuery.") CI ON SC.id = CI.class_id WHERE SC.location_id = ".$locationID." AND section = 'optional' ORDER BY section_position", ARRAY_A);
    }

    public function getShowSectionClassNames($locationID, $section){
        return $this->wpdb->get_col("SELECT DISTINCT(CLASSES.class_name) FROM ".$this->showClassesTable." CLASSES INNER JOIN ".$this->showClassesIndicesTable." INDICES ON CLASSES.id = INDICES.class_id WHERE CLASSES.location_id = ".$locationID." AND section = '".$section."' ORDER BY class_index");
    }

    public function getClassIndex($locationID, $className, $age){
        $classID = $this->getClassID($locationID, $className);
        return $this->wpdb->get_var("SELECT class_index FROM ".$this->showClassesIndicesTable." WHERE class_id = ".$classID." AND age = '".$age."'");
    }

    public function getChallengeIndex($locationID, $challengeName, $age){
        return $this->wpdb->get_var("SELECT challenge_index FROM ".$this->showChallengesIndicesTable." WHERE location_id = ".$locationID." AND challenge_name = '".$challengeName."' AND age = '".$age."'");
    }

    public function swapClassIndices($locationID, $firstClassName, $secondClassName, $age){
        $firstClassID = $this->getClassID($locationID, $firstClassName);
        $secondClassID = $this->getClassID($locationID, $secondClassName);
        $swapQuery = "UPDATE ".$this->showClassesIndicesTable." SET class_index = (SELECT SUM(class_index) FROM (SELECT * FROM ".$this->showClassesIndicesTable.") AS INDICES WHERE INDICES.age = '".$age."' AND INDICES.class_id IN (".$firstClassID.", ".$secondClassID.")) - class_index WHERE age = '".$age."' AND class_id IN (".$firstClassID.", ".$secondClassID.")";
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
