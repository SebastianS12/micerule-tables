<?php

abstract class SinglePlacement{
    protected $placementTable;

    public function addPlacement($entryID, $placement, $locationID){
        global $wpdb;
        $indexID = $this->getClassIndexID($entryID, $locationID);
        $wpdb->insert($this->placementTable, array("entry_id" => $entryID, "placement" => $placement, "class_index_id" => $indexID));
    }

    public function removePlacement($entryID){
        global $wpdb;
        $wpdb->delete($this->placementTable, array("entry_id" => $entryID));
    }

    abstract protected function getClassIndexID($entryID, $locationID);
}

class ClassPlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_class_placements";
    }

    protected function getClassIndexID($entryID, $locationID)
    {
        global $wpdb;
        $showEntry = ShowEntry::createWithEntryID($entryID);
        return $wpdb->get_var("SELECT INDICES.id FROM ".$wpdb->prefix."micerule_show_classes_indices INDICES
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                               WHERE class_name = '".$showEntry->className."' AND age = '".$showEntry->age."' AND location_id = ".$locationID);
    }
}

class JuniorPlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_junior_placements";
    }

    protected function getClassIndexID($entryID, $locationID)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT INDICES.id FROM ".$wpdb->prefix."micerule_show_classes_indices INDICES
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                               WHERE class_name = 'Junior' AND location_id = ".$locationID);
    }
}

class SectionPlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_section_placements";
    }

    protected function getClassIndexID($entryID, $locationID)
    {
        global $wpdb;
        $showEntry = ShowEntry::createWithEntryID($entryID);
        return $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_challenges_indices INDICES
                               WHERE challenge_name = '".EventProperties::getChallengeName($showEntry->sectionName)."' AND age = '".$showEntry->age."' AND location_id = ".$locationID);
    }
}

class GrandChallengePlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_grand_challenge_placements";
    }

    protected function getClassIndexID($entryID, $locationID)
    {
        global $wpdb;
        $showEntry = ShowEntry::createWithEntryID($entryID);
        return $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_challenges_indices INDICES
                               WHERE challenge_name = '".EventProperties::GRANDCHALLENGE."' AND age = '".$showEntry->age."' AND location_id = ".$locationID);
    }
}