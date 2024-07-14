<?php

class GeneralComment{
    private $id;
    private $eventPostID;
    private $judgeNo;
    public $comment;

    private function __construct($eventPostID, $judgeNo)
    {
        $this->id = $this->getID($eventPostID, $judgeNo);
        $this->eventPostID = $eventPostID;
        $this->judgeNo = $judgeNo;
    }

    private function getID($eventPostID, $judgeNo){
        global $wpdb;
        $id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_judges_general_comments
                                WHERE event_post_id = ".$eventPostID." AND judge_no = ".$judgeNo);
        return $id;
    }

    public static function loadFromDB($eventPostID, $judgeNo){
        global $wpdb;
        $classComment = new GeneralComment($eventPostID, $judgeNo);
        $comment = $wpdb->get_var("SELECT comment FROM ".$wpdb->prefix."micerule_show_judges_general_comments GC
                                    INNER JOIN ".$wpdb->prefix."micerule_event_judges EJ ON GC.event_post_id = EJ.event_post_id AND GC.judge_no = EJ.judge_no 
                                    WHERE EJ.event_post_id = ".$eventPostID." AND EJ.judge_no = ".$judgeNo);
        $classComment->comment = (isset($comment)) ? $comment : "";

        return $classComment;
    }

    public static function create($eventPostID, $judgeNo, $comment){
        $classComment = new GeneralComment($eventPostID, $judgeNo);
        $classComment->comment = $comment;
        return $classComment;
    }

    public function saveToDB(){
        global $wpdb;
        $data = array("event_post_id" => $this->eventPostID,
                      "judge_no" => $this->judgeNo,
                      "comment" => $this->comment,);
        if(!isset($this->id))
            $wpdb->insert($wpdb->prefix."micerule_show_judges_general_comments", $data);
        else
            $wpdb->update($wpdb->prefix."micerule_show_judges_general_comments", $data, array('id' => $this->id));
    }
}