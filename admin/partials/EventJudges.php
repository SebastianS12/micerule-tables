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
}
