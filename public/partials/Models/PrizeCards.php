<?php

class PrizeCardFactory
{
    public static function createPrizeCard($eventPostID, $prizeCardData)
    {
        if ($prizeCardData['prize'] == "Class")
            return new ClassPrizeCard($eventPostID, $prizeCardData);
        if ($prizeCardData['prize'] == "Section Challenge")
            return new SectionPrizeCard($eventPostID, $prizeCardData);
        if ($prizeCardData['prize'] == "Grand Challenge")
            return new GrandChallengePrizeCard($eventPostID, $prizeCardData);
    }
}

abstract class PrizeCard
{
    public $eventPostID;
    public $placement;
    public $prize;
    public $age;
    public $userName;
    public $classIndex;
    public $varietyName;
    public $penNumber;
    public $className;
    public $section;
    public $judge;
    public $entryCount;
    public $date;
    public $prizeClass;
    public $placementClass;
    public $displayedPlacement;

    const placementClasses = array("1" => "first", "2" => "second", "3" => "third");

    public function __construct($eventPostID, $prizeCardData)
    {
        $this->eventPostID = $eventPostID;
        $this->loadPrizeCardData($prizeCardData);
    }

    private function loadPrizeCardData($prizeCardData)
    {
        $this->placement = $prizeCardData['placement'];
        $this->prize = $prizeCardData['prize'];
        $this->age = $prizeCardData['age'];
        $this->userName = $prizeCardData['user_name'];
        $this->classIndex = $prizeCardData['class_index'];
        $this->varietyName = $prizeCardData['variety_name'];
        $this->penNumber = $prizeCardData['pen_number'];
        $this->className = $prizeCardData['class_name'];
        $this->section = $prizeCardData['section'];
        $this->judge = $this->getJudge($this->eventPostID);
        $this->entryCount = $this->getEntryCount($this->eventPostID);
        $this->date = $this->getDate();
        $this->placementClass = self::placementClasses[$prizeCardData['placement']];
        $this->displayedPlacement = $this->getDisplayedPlacement($prizeCardData);
    }

    abstract protected function getJudge();
    abstract protected function getEntryCount();
    abstract protected function getDisplayedPlacement($prizeCardData);
    abstract protected function updatePrinted($printed);

    private function getDate()
    {
        return date("d/m/Y");
    }
}

class ClassPrizeCard extends PrizeCard
{
    public function __construct($eventPostID, $prizeCardData)
    {
        parent::__construct($eventPostID, $prizeCardData);
        $this->prizeClass = "breed-class";
    }

    protected function getJudge()
    {
        return EventJudgesHelper::getSectionJudge($this->eventPostID, $this->section);
    }

    protected function getEntryCount()
    {
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getClassRegistrationCount($this->eventPostID, $this->className, $this->age);
    }

    protected function getDisplayedPlacement($prizeCardData)
    {
        return $prizeCardData['placement'];
    }

    public function updatePrinted($printed){
        global $wpdb;
        $placementEntry = ShowEntry::createWithPenNumber($this->eventPostID, $this->penNumber);
        $wpdb->update($wpdb->prefix."micerule_show_class_placements", array("printed" => $printed), array("entry_id" => $placementEntry->ID));
    }
}

class SectionPrizeCard extends PrizeCard
{
    public function __construct($eventPostID, $prizeCardData)
    {
        parent::__construct($eventPostID, $prizeCardData);
        $this->prizeClass = "section-challenge";
    }

    protected function getJudge()
    {
        return EventJudgesHelper::getSectionJudge($this->eventPostID, $this->section);
    }

    protected function getEntryCount()
    {
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getSectionRegistrationCount($this->eventPostID, $this->section, $this->age);
    }

    protected function getDisplayedPlacement($prizeCardData)
    {
        $displayedPlacement = $prizeCardData['placement'];
        if ($prizeCardData['placement'] == 1 && $prizeCardData['award'] == "BIS")
            $displayedPlacement = "BISec";
        if ($prizeCardData['placement'] == 1 && $prizeCardData['award'] == "BOA")
            $displayedPlacement = "BOSec";

        return $displayedPlacement;
    }

    public function updatePrinted($printed){
        global $wpdb;
        $placementEntry = ShowEntry::createWithPenNumber($this->eventPostID, $this->penNumber);
        $wpdb->update($wpdb->prefix."micerule_show_section_placements", array("printed" => $printed), array("entry_id" => $placementEntry->ID));
    }
}

class GrandChallengePrizeCard extends PrizeCard
{
    public function __construct($eventPostID, $prizeCardData)
    {
        parent::__construct($eventPostID, $prizeCardData);
        $this->prizeClass = "grand-challenge";
    }

    protected function getJudge()
    {
        return EventJudgesHelper::getGrandChallengeJudges($this->eventPostID);
    }

    protected function getEntryCount()
    {
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getGrandChallengeRegistrationCount($this->eventPostID, $this->age);
    }

    protected function getDisplayedPlacement($prizeCardData)
    {
        $displayedPlacement = $prizeCardData['placement'];
        if ($prizeCardData['placement'] == 1 && $prizeCardData['award'] == "BIS")
            $displayedPlacement = "BIS";
        if ($prizeCardData['placement'] == 1 && $prizeCardData['award'] == "BOA")
            $displayedPlacement = "BOA";

        return $displayedPlacement;
    }

    public function updatePrinted($printed){
        global $wpdb;
        $placementEntry = ShowEntry::createWithPenNumber($this->eventPostID, $this->penNumber);
        $wpdb->update($wpdb->prefix."micerule_show_grand_challenge_placements", array("printed" => $printed), array("entry_id" => $placementEntry->ID));
    }
}
