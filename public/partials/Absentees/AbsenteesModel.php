<?php

class AbsenteesModel{
    public static function getAbsentees($eventPostID, $judgeName){
        global $wpdb;
        return $wpdb->get_results("SELECT pen_number, class_index FROM sm1_micerule_show_entries ENTRIES
                                    INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON CLASSES.id = INDICES.class_id AND REGISTRATIONS.age = INDICES.age
                                    INNER JOIN ".$wpdb->prefix."micerule_event_judges_sections JUDGE_SECTIONS ON CLASSES.section = JUDGE_SECTIONS.section
                                    INNER JOIN ".$wpdb->prefix."micerule_event_judges JUDGES ON JUDGE_SECTIONS.judge_no = JUDGES.judge_no AND JUDGE_SECTIONS.event_post_id = JUDGES.event_post_id
                                    WHERE JUDGES.event_post_id = ".$eventPostID." AND JUDGES.judge_name = '".$judgeName."' AND absent = TRUE", ARRAY_A);
    }
}