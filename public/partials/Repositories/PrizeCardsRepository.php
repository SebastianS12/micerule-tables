<?php

class PrizeCardsRepository{
    public function getAll($eventPostID, $locationID){
        global $wpdb;
        $query = <<<SQL
                    WITH
                    GrandChallengeData AS (
                        SELECT INDICES.id AS challenge_id, challenge_index as index_number, GC_COUNT.entry_count, section, INDICES.age, INDICES.challenge_name AS class_name
                            FROM 
                                {$wpdb->prefix}micerule_show_challenges_indices INDICES
                            INNER JOIN (
                                SELECT REGISTRATIONS.age, COUNT(*) AS entry_count
                                FROM 
                                    {$wpdb->prefix}micerule_show_user_registrations REGISTRATIONS
                                INNER JOIN 
                                    {$wpdb->prefix}micerule_show_user_registrations_order REGISTRATIONS_ORDER 
                                    ON REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
                                WHERE 
                                    REGISTRATIONS.event_post_id = {$eventPostID}
                                GROUP BY 
                                    REGISTRATIONS.age
                            ) GC_COUNT 
                            ON GC_COUNT.age = INDICES.age
                            WHERE challenge_name = "GRAND CHALLENGE" AND INDICES.location_id = {$locationID}
                        ),

                        SectionChallengeData AS (
                            SELECT INDICES.id AS challenge_id, challenge_index as index_number, COUNT(*) AS entry_count, INDICES.section, INDICES.age, INDICES.challenge_name as class_name
                            FROM 
                                {$wpdb->prefix}micerule_show_challenges_indices INDICES
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_classes CLASSES 
                                ON INDICES.section = CLASSES.section AND INDICES.location_id = CLASSES.location_id
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_user_registrations REGISTRATIONS 
                                ON REGISTRATIONS.class_id = CLASSES.id AND REGISTRATIONS.age = INDICES.age
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_user_registrations_order REGISTRATIONS_ORDER 
                                ON REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
                            WHERE 
                                INDICES.location_id = {$locationID}
                            GROUP BY 
                                INDICES.id
                        ),
                        
                        PlacementsWithAwards AS (
                            SELECT 
                                Awards.id as placement_id, entry_id, index_id, Awards.prize, placement, Awards.printed, award FROM {$wpdb->prefix}micerule_show_challenge_placements Placements
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_challenge_awards Awards 
                            ON 
                                Placements.id = Awards.challenge_placement_id
                            UNION SELECT *, "" as award FROM {$wpdb->prefix}micerule_show_challenge_placements
                        ),

                        ChallengePlacementsData AS (
                            SELECT 
                                *
                            FROM 
                                PlacementsWithAwards
                            INNER JOIN (
                                -- Combine both GrandChallengeCounts and ClassChallengeCounts using UNION
                                SELECT * FROM GrandChallengeData
                                UNION
                                SELECT * FROM SectionChallengeData
                            ) CHALLENGE_DATA 
                            ON 
                                PlacementsWithAwards.index_id = CHALLENGE_DATA.challenge_id
                        ),
                        
                        ClassRegistrationCount AS (
                            SELECT 
                                INDICES.id AS count_index_id, class_index AS index_number, COUNT(*) AS entry_count, section, INDICES.age, CLASSES.class_name as class_name
                            FROM 
                                {$wpdb->prefix}micerule_show_classes_indices INDICES
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_user_registrations REGISTRATIONS ON REGISTRATIONS.class_id = INDICES.class_id AND REGISTRATIONS.age = INDICES.age
                            INNER JOIN 
                                {$wpdb->prefix}micerule_show_user_registrations_order REGISTRATIONS_ORDER ON REGISTRATIONS.class_registration_id = 				REGISTRATIONS_ORDER.class_registration_id
                            GROUP BY INDICES.id
                        ),
                        
                        ClassPlacementsData AS (
                            SELECT Placements.id as placement_id, entry_id, index_id, prize, placement, printed, "" as award, count_index_id, index_number, entry_count, section, age, class_name
                            FROM 
                                {$wpdb->prefix}micerule_show_class_placements Placements
                            INNER JOIN
                                ClassRegistrationCount
                            ON
                                Placements.index_id = ClassRegistrationCount.count_index_id
                            WHERE
                                prize != 1
                        ),

                        JuniorPlacementsData As (
                            SELECT 
                            placements.id AS placement_id, 
                            entry_id,
                            index_id,
                            prize,
                            placement,
                            printed,
                            "" AS award,
                            ClassIndices.id as count_index_id,
                            class_index as index_number,
                            (SELECT COUNT(*) 
                            FROM {$wpdb->prefix}micerule_show_user_registrations Registrations 
                            INNER JOIN {$wpdb->prefix}micerule_show_user_junior_registrations JRegistrations
                            ON Registrations.class_registration_id = JRegistrations.class_registration_id
                            WHERE event_post_id = {$eventPostID}
                            ) AS entry_count,
                            section,
                            age,
                            class_name
                        FROM 
                            {$wpdb->prefix}micerule_show_class_placements placements
                        INNER JOIN 
                            {$wpdb->prefix}micerule_show_classes_indices ClassIndices
                        ON 
                            placements.index_id = ClassIndices.id
                        INNER JOIN 
                            {$wpdb->prefix}micerule_show_classes Classes
                        ON 
                            ClassIndices.class_id = Classes.id
                        WHERE 
                            prize = 1 AND placement = 1 AND location_id = {$locationID}
                        )

                        SELECT placement_id, placement, prize, PlacementsData.age, user_name, class_name, variety_name, pen_number, index_number, PlacementsData.section, printed, judge_name, entry_count, award FROM
                        (SELECT * FROM 
                            ClassPlacementsData 
                            UNION SELECT * FROM 
                            ChallengePlacementsData
                            UNION SELECT * FROM
                            JuniorPlacementsData) PlacementsData
                        INNER JOIN 
                            {$wpdb->prefix}micerule_show_entries Entries 
                        ON
                            Entries.id = PlacementsData.entry_id
                        INNER JOIN 
                            {$wpdb->prefix}micerule_show_user_registrations Registrations 
                        ON 
                            Entries.class_registration_id = Registrations.class_registration_id
                        LEFT JOIN 
                        (SELECT judge_name, section FROM 
                            {$wpdb->prefix}micerule_event_judges_sections JudgesSections
                        LEFT JOIN 
                            {$wpdb->prefix}micerule_event_judges Judges 
                        ON 
                            Judges.judge_no = JudgesSections.judge_no AND Judges.event_post_id = JudgesSections.event_post_id 
                        WHERE 
                            Judges.event_post_id = {$eventPostID})JudgesData
                        ON PlacementsData.section = JudgesData.section
                        ORDER BY user_name, index_number, placement
                    SQL;

        return $wpdb->get_results($query, ARRAY_A);
    }

    public function updatePrinted(int $id, bool $printed, IPrintDAO $printDAO){
        $printDAO->updatePrinted($id, $printed);
    }
}