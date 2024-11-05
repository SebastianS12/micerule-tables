<?php

class PrizeCardsModel{
    public function getPrizeCards($eventPostID, $printed){
        global $wpdb;
        return $wpdb->get_results("SELECT placement_id, placement, award, prize, age, user_name, class_index, variety_name, pen_number, PLACEMENTS.class_name, section, printed FROM 
                                    (SELECT class_placement_id as placement_id, entry_id, placement, '' as award, 'Class' as prize, class_index, class_name, printed FROM ".$wpdb->prefix."micerule_show_class_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                                    UNION
                                    SELECT section_placement_id as placement_id, entry_id, placement, award, 'Section Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_section_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    UNION
                                    SELECT grand_challenge_placement_id as placement_id, entry_id, placement, award, 'Grand Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_grand_challenge_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    UNION
                                    SELECT class_placement_id as placement_id, entry_id, placement, '' as award, 'Junior Challenge' as prize, class_index, class_name, printed FROM ".$wpdb->prefix."micerule_show_junior_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                                    WHERE placement = 1) PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_entries ENTRIES ON PLACEMENTS.entry_id = ENTRIES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                    WHERE printed = ".var_export($printed, true)." AND event_post_id = ".$eventPostID." ORDER BY user_name, class_index, placement", ARRAY_A);
    }

    //TODO: move function to PrizeCard Model -> no need for joins
    public function getSinglePrizeCard($placementID, $prize){
        global $wpdb;
        return $wpdb->get_row("SELECT placement_id, placement, award, prize, age, user_name, class_index, variety_name, pen_number, PLACEMENTS.class_name, section, printed FROM 
                                    (SELECT class_placement_id as placement_id, entry_id, placement, '' as award, 'Class' as prize, class_index, class_name, printed FROM ".$wpdb->prefix."micerule_show_class_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                                    UNION
                                    SELECT section_placement_id as placement_id, entry_id, placement, award, 'Section Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_section_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    UNION
                                    SELECT grand_challenge_placement_id as placement_id, entry_id, placement, award, 'Grand Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_grand_challenge_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    UNION
                                    SELECT class_placement_id as placement_id, entry_id, placement, '' as award, 'Junior Challenge' as prize, class_index, class_name, printed FROM ".$wpdb->prefix."micerule_show_junior_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id) PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_entries ENTRIES ON PLACEMENTS.entry_id = ENTRIES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                    WHERE prize = '".$prize."' AND placement_id = ".$placementID, ARRAY_A);
    }
}

