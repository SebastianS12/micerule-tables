<?php

class PlacementReport{
    private $id;
    public $eventPostID;
    public $classIndexID;
    public $gender;
    public $comment;
    public $placementID;

    private function __construct($eventPostID, $className, $age, $placementID){
        global $wpdb;
        $this->eventPostID = $eventPostID;
        $this->placementID = $placementID;
        $this->classIndexID = $wpdb->get_var("SELECT CI.id FROM ".$wpdb->prefix."micerule_show_classes SC
                                              INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices CI ON SC.id = CI.class_id
                                              WHERE SC.class_name = '".$className."' AND SC.location_id = ".EventProperties::getEventLocationID($eventPostID)." AND CI.age = '".$age."'");
        $this->id = $this->getID($eventPostID, $this->classIndexID, $placementID);
    }

    private function getID($eventPostID, $classIndexID, $placementID){
        global $wpdb;
        $id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_judges_class_reports
                                WHERE event_post_id = ".$eventPostID." AND class_index_id = ".$classIndexID." AND placement_id = ".$placementID);
        return $id;
    }

    public static function loadFromDB($eventPostID, $className, $age, $placementID){
        $placementReport = new PlacementReport($eventPostID, $className, $age, $placementID);
        $reportData = self::getReportData($eventPostID, $className, $age, $placementID);
        $placementReport->gender = (isset($reportData['gender'])) ? $reportData["gender"] : "";
        $placementReport->comment = (isset($reportData['comment'])) ? $reportData["comment"] : "";

        return $placementReport;
    }

    public static function create($eventPostID, $className, $age, $placementID, $gender, $comment){
        $placementReport = new PlacementReport($eventPostID, $className, $age, $placementID);
        $placementReport->gender = $gender;
        $placementReport->comment = $comment;
        return $placementReport;
    }

    private static function getReportData($eventPostID, $className, $age, $placementID){
        global $wpdb;
        $reportData = $wpdb->get_row("SELECT comment, gender FROM ".$wpdb->prefix."micerule_show_judges_class_reports CR 
                                      INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices CI ON CR.class_index_id = CI.id 
                                      INNER JOIN ".$wpdb->prefix."micerule_show_classes SC ON CI.class_id = SC.id 
                                      WHERE CR.event_post_id = ".$eventPostID." AND SC.class_name = '".$className."' AND CI.age = '".$age."' AND EJ.judge_no = ".$judgeNo." AND CR.placement = ".$placement, ARRAY_A);
        return $reportData;
    }

    public function saveToDB(){
        global $wpdb;
        $data = array("class_index_id" => $this->classIndexID,
                      "event_post_id" => $this->eventPostID,
                      "judge_no" => $this->judgeNo,
                      "gender" => $this->gender,
                      "comment" => $this->comment,
                      "placement" => $this->placement,);
        if(!isset($this->id))
            $wpdb->insert($wpdb->prefix."micerule_show_judges_class_reports", $data);
        else
            $wpdb->update($wpdb->prefix."micerule_show_judges_class_reports", $data, array('id' => $this->id));
    }
}