<?php

abstract class SinglePlacement{
    protected $placementTable;

    public function addPlacement($entryID, $placement){
        global $wpdb;
        $wpdb->insert($this->placementTable, array("entry_id" => $entryID, "placement" => $placement));
    }

    public function removePlacement($entryID){
        global $wpdb;
        $wpdb->delete($this->placementTable, array("entry_id" => $entryID));
    }
}

class ClassPlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_class_placements";
    }
}

class JuniorPlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_junior_placements";
    }
}

class SectionPlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_section_placements";
    }
}

class GrandChallengePlacement extends SinglePlacement{
    public function __construct()
    {
        global $wpdb;
        $this->placementTable = $wpdb->prefix."micerule_show_grand_challenge_placements";
    }
}