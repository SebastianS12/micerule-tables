<?php

//TODO: Rename
class EventJudgesHelper
{
    public static function saveEventJudges($eventPostID, $eventJudgesData)
    {
        foreach ($eventJudgesData as $judgeNo => $judgeData) {
            self::saveJudgeData($eventPostID, $judgeNo, $judgeData);
        }
    }

    private static function saveJudgeData($eventPostID, $judgeNo, $judgeData)
    {
        if (isset($judgeData['name']) && $judgeData['name'] != "") {
            $judgeName = $judgeData['name'];
            self::saveJudgeName($eventPostID, $judgeNo, $judgeName);

            foreach(EventProperties::SECTIONNAMES as $sectionName){
                if(isset($judgeData['sections'][strtolower($sectionName)]) && $judgeData['sections'][strtolower($sectionName)] == "on")
                    self::saveJudgeSection($eventPostID, $judgeNo, strtolower($sectionName));
                else
                    self::deleteJudgeSection($eventPostID, $judgeNo, strtolower($sectionName));
            }
        
            if (isset($judgeData['partnership']) && $judgeData['partnership'] != "")
                self::saveJudgePartner($eventPostID, $judgeNo, $judgeData['partnership']);
            else
                self::deleteJudgePartner($eventPostID, $judgeNo);
        }else{
            self::deleteJudgeName($eventPostID, $judgeNo);
        }
    }

    public static function saveJudgeName($eventPostID, $judgeNo, $judgeName){
        global $wpdb;
        $table_name = $wpdb->prefix."micerule_event_judges";
            $data = array(
                "event_post_id" => $eventPostID,
                "judge_no" => $judgeNo, 
                "judge_name" => $judgeName);

            $updateStatus = $wpdb->update($table_name, $data, array("event_post_id" => $eventPostID, "judge_no" => $judgeNo));
            if($updateStatus == 0)
                $wpdb->insert($table_name, $data);
    }
    
    public static function deleteJudgeName($eventPostID, $judgeNo){
        global $wpdb;
        $table_name = $wpdb->prefix."micerule_event_judges";
        $wpdb->delete($table_name, array("event_post_id" => $eventPostID, "judge_no" => $judgeNo));
    }

    public static function saveJudgeSection($eventPostID, $judgeNo, $section){
        global $wpdb;
        $table_name = $wpdb->prefix."micerule_event_judges_sections";
        $data = array(
            "event_post_id" => $eventPostID,
            "judge_no" => $judgeNo,
            "section" => $section,
        );

        if(count($wpdb->get_results("SELECT * FROM ".$table_name." WHERE event_post_id = ".$eventPostID." AND judge_no = ".$judgeNo." AND section = '".$section."'")) == 0)
            $wpdb->insert($table_name, $data);
    }

    public static function deleteJudgeSection($eventPostID, $judgeNo, $section){
        global $wpdb;
        $table_name = $wpdb->prefix."micerule_event_judges_sections";
        $wpdb->delete($table_name, array("event_post_id" => $eventPostID, "judge_no" => $judgeNo, "section" => $section));
    }

    public static function saveJudgePartner($eventPostID, $judgeNo, $partnerName){
        global $wpdb;
        $table_name = $wpdb->prefix . "micerule_event_judges_partnerships";
        $data = array(
            "event_post_id" => $eventPostID,
            "judge_no" => $judgeNo,
            "partner_name" => $partnerName,
        );
        $updateStatus = $wpdb->update($table_name, $data, array("event_post_id" => $eventPostID, "judge_no" => $judgeNo));
        if($updateStatus == 0)
            $wpdb->insert($table_name, $data);
    }

    public static function deleteJudgePartner($eventPostID, $judgeNo){
        global $wpdb;
        $table_name = $wpdb->prefix . "micerule_event_judges_partnerships";
        $wpdb->delete($table_name, array("event_post_id" => $eventPostID, "judge_no" => $judgeNo));
    }

    public static function getEventJudgeNames($eventPostID){
        global $wpdb;
        return $wpdb->get_col("SELECT judge_name FROM ".$wpdb->prefix."micerule_event_judges WHERE event_post_id = ".$eventPostID);
    }

    public static function getJudgeSections($eventPostID, $judgeName){
        global $wpdb;
        return $wpdb->get_col("SELECT section FROM ".$wpdb->prefix."micerule_event_judges_sections SECTIONS
                               INNER JOIN ".$wpdb->prefix."micerule_event_judges JUDGES ON SECTIONS.event_post_id = JUDGES.event_post_id AND SECTIONS.judge_no = JUDGES.judge_no
                               WHERE JUDGES.event_post_id = ".$eventPostID." AND judge_name = '".$judgeName."'");
    }

    public static function getSectionJudge($eventPostID, $section){
        global $wpdb;
        return $wpdb->get_var("SELECT judge_name FROM ".$wpdb->prefix."micerule_event_judges JUDGES
                               INNER JOIN ".$wpdb->prefix."micerule_event_judges_sections SECTIONS ON JUDGES.event_post_id = SECTIONS.event_post_id AND JUDGES.judge_no = SECTIONS.judge_no
                               WHERE section = '".$section."' AND SECTIONS.event_post_id = ".$eventPostID);
    }

    public static function getGrandChallengeJudges($eventPostID){
        $grandChallengeJudgeName = "";
        foreach (self::getEventJudgeNames($eventPostID) as $judgeName) {
            $grandChallengeJudgeName .= $judgeName . "  ";
        }
        
        return $grandChallengeJudgeName;
    }

    public static function convertPostMeta(){
        global $wpdb;
        $postMetaResults = $wpdb->get_results("SELECT post_id, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'micerule_data_settings' AND meta_value IS NOT NULL", ARRAY_A);
        foreach($postMetaResults as $eventResult){
            $eventPostID = $eventResult['post_id'];
            $metaValue = get_post_meta($eventPostID, 'micerule_data_settings', true);
                foreach($metaValue['judges'] as $index => $judgeName){
                    if($judgeName != ""){
                        self::saveJudgeName($eventPostID, $index+1, $judgeName);
                        foreach($metaValue['classes'][$index] as $section){
                            self::saveJudgeSection($eventPostID, $index+1, strtolower($section));
                        }
                        if($metaValue['pShip'][$index] != ""){
                            self::saveJudgePartner($eventPostID, $index+1, $metaValue['pShip'][$index]);
                        }
                    }
                }
        }
    }
}
