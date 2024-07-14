<?php

class ClassComment{
    private $id;
    public $eventPostID;
    public $classIndexID;
    public $judgeNo;
    public $comment;

    private function __construct($eventPostID, $className, $age, $judgeNo)
    {
        global $wpdb;
        $this->eventPostID = $eventPostID;
        $this->judgeNo = $judgeNo;
        //TODO: Code Duplication (PlacementReport), Helper Class? Model Class?
        $this->classIndexID = $wpdb->get_var("SELECT CI.id FROM ".$wpdb->prefix."micerule_show_classes SC
                                              INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices CI ON SC.id = CI.class_id
                                              WHERE SC.class_name = '".$className."' AND SC.location_id = ".EventProperties::getEventLocationID($eventPostID)." AND CI.age = '".$age."'");
        $this->id = $this->getID($eventPostID, $judgeNo, $this->classIndexID);
    }

    private function getID($eventPostID, $judgeNo, $classIndexID){
        global $wpdb;
        $id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_judges_class_comments
                                WHERE event_post_id = ".$eventPostID." AND judge_no = ".$judgeNo." AND class_index_id = ".$classIndexID);
        return $id;
    }

    public static function loadFromDB($eventPostID, $className, $age, $judgeNo){
        global $wpdb;
        $classComment = new ClassComment($eventPostID, $className, $age, $judgeNo);
        $comment = $wpdb->get_var("SELECT comment FROM ".$wpdb->prefix."micerule_show_judges_class_comments CC
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices CI ON CC.class_index_id = CI.id 
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes SC ON CI.class_id = SC.id 
                                    INNER JOIN ".$wpdb->prefix."micerule_event_judges EJ ON CC.event_post_id = EJ.event_post_id AND CC.judge_no = EJ.judge_no 
                                    WHERE EJ.event_post_id = ".$eventPostID." AND SC.class_name = '".$className."' AND CI.age = '".$age."' AND EJ.judge_no = ".$judgeNo);
        $classComment->comment = (isset($comment)) ? $comment : "";

        return $classComment;
    }

    public static function create($eventPostID, $className, $age, $judgeNo, $comment){
        $classComment = new ClassComment($eventPostID, $className, $age, $judgeNo);
        $classComment->comment = $comment;
        return $classComment;
    }

    public function saveToDB(){
        global $wpdb;
        $data = array("class_index_id" => $this->classIndexID,
                      "event_post_id" => $this->eventPostID,
                      "judge_no" => $this->judgeNo,
                      "comment" => $this->comment,);
        if(!isset($this->id))
            $wpdb->insert($wpdb->prefix."micerule_show_judges_class_comments", $data);
        else
            $wpdb->update($wpdb->prefix."micerule_show_judges_class_comments", $data, array('id' => $this->id));
    }
}