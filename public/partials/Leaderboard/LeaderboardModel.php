<?php 

class LeaderboardModel{
    private $wpdb;
    private $userTable;
    private $eventResultTable;
    private $eventTable;
    private $seasonResultTable;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setDatabase();
    }

    private function setDatabase(){
        $this->userTable = $this->wpdb->users;
        $this->eventResultTable = $this->wpdb->prefix."micerule_event_results";
        $this->eventTable = $this->wpdb->prefix."em_events";
        $this->seasonResultTable = $this->wpdb->prefix."micerule_result_tables";
    }

    public function getUserID($userName){
        return $this->wpdb->get_var("SELECT ID FROM ".$this->userTable." WHERE display_name = '".$userName."'");
    }

    public function getSeasonVarietyData($seasonStartDate, $seasonEndDate){
        return $this->wpdb->get_results("SELECT SUM(points) AS variety_total, variety_name FROM ".$this->eventResultTable." EVENTRESULTS INNER JOIN ".$this->eventTable." EVENTS ON EVENTRESULTS.event_post_id = EVENTS.post_id WHERE UNIX_TIMESTAMP(event_start_date) >= ".$seasonStartDate." AND UNIX_TIMESTAMP(event_end_date) <= ".$seasonEndDate." AND variety_name != '' GROUP BY variety_name ORDER BY variety_total DESC", ARRAY_A);
    }

    public function getPreviousSeasonDates($currentSeasonStartDate){
        return $this->wpdb->get_row("SELECT dateFrom, dateTo FROM ".$this->seasonResultTable." WHERE dateTo < ".$currentSeasonStartDate." ORDER BY dateTo DESC LIMIT 1", ARRAY_A);
    }
}