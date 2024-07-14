<?php

class PlacementReport{
    private $id;
    public $eventPostID;
    public $classIndexID;
    public $judgeNo;
    public $gender;
    public $comment;
    public $placement;

    private function __construct($eventPostID, $className, $age, $judgeNo, $placement){
        global $wpdb;
        $this->eventPostID = $eventPostID;
        $this->judgeNo = $judgeNo;
        $this->placement = $placement;
        $this->classIndexID = $wpdb->get_var("SELECT CI.id FROM ".$wpdb->prefix."micerule_show_classes SC
                                              INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices CI ON SC.id = CI.class_id
                                              WHERE SC.class_name = '".$className."' AND SC.location_id = ".EventProperties::getEventLocationID($eventPostID)." AND CI.age = '".$age."'");
        $this->id = $this->getID($eventPostID, $judgeNo, $this->classIndexID, $placement);
    }

    private function getID($eventPostID, $judgeNo, $classIndexID, $placement){
        global $wpdb;
        $id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_judges_class_reports
                                WHERE event_post_id = ".$eventPostID." AND judge_no = ".$judgeNo." AND class_index_id = ".$classIndexID." AND placement = ".$placement);
        return $id;
    }

    public static function loadFromDB($eventPostID, $className, $age, $judgeNo, $placement){
        $placementReport = new PlacementReport($eventPostID, $className, $age, $judgeNo, $placement);
        $reportData = self::getReportData($eventPostID, $className, $age, $judgeNo, $placement);
        $placementReport->gender = (isset($reportData['gender'])) ? $reportData["gender"] : "";
        $placementReport->comment = (isset($reportData['comment'])) ? $reportData["comment"] : "";

        return $placementReport;
    }

    public static function create($eventPostID, $className, $age, $judgeNo, $placement, $gender, $comment){
        $placementReport = new PlacementReport($eventPostID, $className, $age, $judgeNo, $placement);
        $placementReport->gender = $gender;
        $placementReport->comment = $comment;
        return $placementReport;
    }

    private static function getReportData($eventPostID, $className, $age, $judgeNo, $placement){
        global $wpdb;
        $reportData = $wpdb->get_row("SELECT comment, gender FROM ".$wpdb->prefix."micerule_show_judges_class_reports CR 
                                      INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices CI ON CR.class_index_id = CI.id 
                                      INNER JOIN ".$wpdb->prefix."micerule_show_classes SC ON CI.class_id = SC.id 
                                      INNER JOIN ".$wpdb->prefix."micerule_event_judges EJ ON CR.event_post_id = EJ.event_post_id AND CR.judge_no = EJ.judge_no 
                                      WHERE EJ.event_post_id = ".$eventPostID." AND SC.class_name = '".$className."' AND CI.age = '".$age."' AND EJ.judge_no = ".$judgeNo." AND CR.placement = ".$placement, ARRAY_A);
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