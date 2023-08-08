<?php 

class SeasonResultsModel {
    private $em_event_table;
    private $event_results_table;
    private $judge_table;
    private $judge_partnerships_table;

    function __construct()
    {
        $this->setDatabase();
    }

    private function setDatabase(){
        global $wpdb;
        $this->em_event_table = $wpdb->prefix."em_events";
        $this->event_results_table = $wpdb->prefix."micerule_event_results";
        $this->judge_table = $wpdb->prefix."micerule_event_judges";
        $this->judge_partnerships_table = $wpdb->prefix."micerule_event_judges_partnerships";
    }

    private function getFancierPointsQuery($dateFrom, $dateTo){
        return "SELECT SUM(points) AS points, fancier_name FROM ".$this->em_event_table." INNER JOIN ".$this->event_results_table." ON ".$this->em_event_table.".post_id=".$this->event_results_table.".event_post_id WHERE UNIX_TIMESTAMP(event_start_date) >= ".$dateFrom." AND UNIX_TIMESTAMP(event_end_date) <= ".$dateTo." AND fancier_name != '' GROUP BY fancier_name";
    }

    private function getJudgeNamesQuery($dateFrom, $dateTo){
        return "SELECT judge_name AS name FROM ".$this->em_event_table." INNER JOIN ".$this->judge_table." ON ".$this->em_event_table.".post_id=".$this->judge_table.".event_post_id WHERE UNIX_TIMESTAMP(event_start_date) >= ".$dateFrom." AND UNIX_TIMESTAMP(event_end_date) <= ".$dateTo." AND judge_name != ''";
    }

    private Function getJudgePartnerNamesQuery($dateFrom, $dateTo){
        return "SELECT partner_name AS name FROM ".$this->em_event_table." INNER JOIN ".$this->judge_partnerships_table." ON ".$this->em_event_table.".post_id=".$this->judge_partnerships_table.".event_post_id WHERE UNIX_TIMESTAMP(event_start_date) >= ".$dateFrom." AND UNIX_TIMESTAMP(event_end_date) <= ".$dateTo;
    }

    private function getJudgeCountQuery($dateFrom, $dateTo){
        return "SELECT name, COUNT(*) AS judge_count FROM (".$this->getJudgeNamesQuery($dateFrom, $dateTo)." UNION ALL ".$this->getJudgePartnerNamesQuery($dateFrom, $dateTo).") AS combined_names GROUP BY name";
    }

    public function getFancierSeasonResults($dateFrom, $dateTo){
        global $wpdb;
        return $wpdb->get_results("SELECT points, fancier_name, judge_count FROM (".$this->getFancierPointsQuery($dateFrom, $dateTo).") fancier_points LEFT JOIN (".$this->getJudgeCountQuery($dateFrom, $dateTo).") judge_count ON fancier_points.fancier_name = judge_count.name", ARRAY_A);
    }

    public function getCurrentSeasonDateFrom(){
        global $wpdb;
        $mostRecentSeasonEnd = $wpdb->get_var("SELECT dateTo FROM ".$wpdb->prefix."micerule_result_tables WHERE seasonTable = 1 ORDER BY dateTo DESC");
        if($mostRecentSeasonEnd == null)
            $mostRecentSeasonEnd = time();
            
        return $mostRecentSeasonEnd + 1;
    }

    public function getSeasonTableData(){
        global $wpdb;
        return $wpdb->get_results("SELECT mrtable_id, dateFrom, dateTo, seasonTable FROM ".$wpdb->prefix."micerule_result_tables");
    }

    public function createSeasonTable($dateFrom, $dateTo){
        global $wpdb;
        $data = array(
            'dateFrom'=> $dateFrom,
            'dateTo'=> $dateTo);
            
        $wpdb->insert($wpdb->prefix.'micerule_result_tables', $data);
    }

    public function deleteSeasonTable($id){
        global $wpdb;
        $wpdb->delete($wpdb->prefix.'micerule_result_tables', ['mrtable_id' => $id]);
    }
}