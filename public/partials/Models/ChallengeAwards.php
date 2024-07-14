<?php

abstract class ChallengeAwards
{
    //public $awards;
    public $bis;
    public $boa;
    protected $eventPostID;
    protected $challengeSection;
    protected $placementTable;

    public function __construct($eventPostID, $challengeSection)
    {
        //$this->awards = array("BIS" => "", "BOA" => "");
        $this->loadChallengeAwardsData($eventPostID, $challengeSection);
    }

    private function loadChallengeAwardsData($eventPostID, $challengeSection)
    {
        $this->eventPostID = $eventPostID;
        $this->challengeSection = $challengeSection;
        $this->getAwards();
    }

    private function getAwards()
    {
        $this->bis = $this->getAward("BIS");
        $this->boa = $this->getAward("BOA");
    }

    public function addAwards($age)
    {
        $this->addAward($age, "BIS");
        $this->addAward(EventProperties::getOppositeAge($age), "BOA");
    }

    public function removeAwards()
    {
        if (isset($this->bis) && isset($this->boa)) {
            $this->removeAward($this->bis['entry_id']);
            $this->removeAward($this->boa['entry_id']);
        }
    }

    public function bisChecked($age)
    {
        return ($this->bis != null && $this->bis['age'] == $age);
    }

    public function boaChecked($age)
    {
        return ($this->boa != null && $this->boa['age'] == $age);
    }

    public function addAward($age, $award)
    {
        global $wpdb;
        $entryID = $this->getFirstPlaceEntryID($age);
        if ($entryID != null)
            //$wpdb->update($this->placementTable, array("award" => $award, "printed" => false), array("entry_id" => $entryID));
    }

    public function removeAward($entryID)
    {
        global $wpdb;
        $wpdb->update($this->placementTable, array("award" => null, "printed" => false), array("entry_id" => $entryID));
    }

    abstract protected function getAward($award);
    abstract protected function getFirstPlaceEntryID($age);
}


class SectionChallengeAwards extends ChallengeAwards
{
    public function __construct($eventPostID, $challengeSection)
    {
        parent::__construct($eventPostID, $challengeSection);
        global $wpdb;
        $this->placementTable = $wpdb->prefix . "micerule_show_section_placements";
    }

    public function getAward($award)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT entry_id, age FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                                INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                                INNER JOIN " . $wpdb->prefix . "micerule_show_section_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id
                                INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                WHERE event_post_id = " . $this->eventPostID . " AND section= '" . $this->challengeSection . "' AND award = '" . $award . "'", ARRAY_A);
    }

    protected function getFirstPlaceEntryID($age)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT entry_id FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                        INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                        INNER JOIN " . $wpdb->prefix . "micerule_show_section_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id
                        INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                        WHERE event_post_id = " . $this->eventPostID . " AND section = '" . $this->challengeSection . "' AND placement = 1 AND age = '" . $age . "'");
    }
}

class GrandChallengeAwards extends ChallengeAwards
{
    public function __construct($eventPostID)
    {
        parent::__construct($eventPostID, EventProperties::GRANDCHALLENGE);
        global $wpdb;
        $this->placementTable = $wpdb->prefix . "micerule_show_grand_challenge_placements";
    }

    public function getAward($award)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT entry_id, age FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                                INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                                INNER JOIN " . $wpdb->prefix . "micerule_show_grand_challenge_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id
                                INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                WHERE event_post_id = " . $this->eventPostID . " AND award = '" . $award . "'", ARRAY_A);
    }

    protected function getFirstPlaceEntryID($age)
    {
        global $wpdb;
        echo ($this->eventPostID);
        echo ($this->challengeSection);
        return $wpdb->get_var("SELECT entry_id FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                        INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                        INNER JOIN " . $wpdb->prefix . "micerule_show_grand_challenge_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id
                        INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                        WHERE event_post_id = " . $this->eventPostID . " AND placement = 1 AND age = '" . $age . "'");
    }
}
