<?php

class PrizeCardsModel{
    public function getPrizeCards($printed){
        global $wpdb;
        return $wpdb->get_results("SELECT placement, award, prize, age, user_name, class_index, variety_name, pen_number, PLACEMENTS.class_name, section, printed FROM 
                                    (SELECT entry_id, placement, '' as award, 'Class' as prize, class_index, class_name, printed FROM ".$wpdb->prefix."micerule_show_class_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                                    UNION
                                    SELECT entry_id, placement, award, 'Section Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_section_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    UNION
                                    SELECT entry_id, placement, award, 'Grand Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_grand_challenge_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id) PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_entries ENTRIES ON PLACEMENTS.entry_id = ENTRIES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                    WHERE printed = ".var_export($printed, true), ARRAY_A);
    }

    public function getSinglePrizeCard($printed, $penNumber, $prize){
        global $wpdb;
        return $wpdb->get_row("SELECT placement, award, prize, age, user_name, class_index, variety_name, pen_number, PLACEMENTS.class_name, section, printed FROM 
                                    (SELECT entry_id, placement, '' as award, 'Class' as prize, class_index, class_name, printed FROM ".$wpdb->prefix."micerule_show_class_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                                    UNION
                                    SELECT entry_id, placement, award, 'Section Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_section_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id
                                    UNION
                                    SELECT entry_id, placement, award, 'Grand Challenge' as prize, challenge_index as class_index, challenge_name as class_name, printed FROM ".$wpdb->prefix."micerule_show_grand_challenge_placements PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_challenges_indices INDICES ON PLACEMENTS.class_index_id = INDICES.id) PLACEMENTS
                                    INNER JOIN ".$wpdb->prefix."micerule_show_entries ENTRIES ON PLACEMENTS.entry_id = ENTRIES.id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                                    INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                                    WHERE printed = ".var_export($printed, true)." AND prize = '".$prize."' AND pen_number = ".$penNumber, ARRAY_A);
    }
}
