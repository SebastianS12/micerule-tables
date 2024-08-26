<?php

class JudgesReportModel{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function getJudgeClassesData($eventPostID, $judgeName){
        return $this->wpdb->get_results("SELECT section, class_name, class_index, age, comment, prize FROM (
                                         SELECT JUDGES.event_post_id, location_id, judge_name, CLASSES.section, class_name, class_index, age, comment, 'Class' as prize FROM ".$this->wpdb->prefix."micerule_event_judges JUDGES 
                                         INNER JOIN ".$this->wpdb->prefix."micerule_event_judges_sections JUDGES_SECTIONS ON JUDGES.event_post_id = JUDGES_SECTIONS.event_post_id AND JUDGES.judge_no = JUDGES_SECTIONS.judge_no
                                         INNER JOIN ".$this->wpdb->prefix."micerule_show_classes CLASSES ON JUDGES_SECTIONS.section = CLASSES.section
                                         INNER JOIN ".$this->wpdb->prefix."micerule_show_classes_indices INDICES ON CLASSES.id = INDICES.class_id
                                         LEFT JOIN ".$this->wpdb->prefix."micerule_show_judges_class_comments COMMENTS ON JUDGES.event_post_id = COMMENTS.event_post_id AND INDICES.id = COMMENTS.class_index_id
                                         UNION
                                         SELECT JUDGES.event_post_id, location_id, judge_name, INDICES.section, challenge_name as class_name, challenge_index as class_index, age, '' as comment, 'Section Challenge' as prize FROM ".$this->wpdb->prefix."micerule_event_judges JUDGES 
                                         INNER JOIN ".$this->wpdb->prefix."micerule_event_judges_sections JUDGES_SECTIONS ON JUDGES.event_post_id = JUDGES_SECTIONS.event_post_id AND JUDGES.judge_no = JUDGES_SECTIONS.judge_no
                                         INNER JOIN ".$this->wpdb->prefix."micerule_show_challenges_indices INDICES ON JUDGES_SECTIONS.section= INDICES.section
                                         )JUDGE_CLASSES_DATA
                                         WHERE event_post_id = ".$eventPostID." AND location_id = ".EventProperties::getEventLocationID($eventPostID)." AND judge_name = '".$judgeName."' ORDER BY class_index", ARRAY_A);
    }

    public function submitClassComment($eventPostID, $judgeName, $className){
        
    }
}