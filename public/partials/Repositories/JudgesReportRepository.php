<?php

//TODO: Split into multiple repositories
class JudgesReportRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getJudgesGeneralComments(): array|null{
        global $wpdb;
        $query = <<<SQL
                    SELECT 
                        judge_name, Comments.id as comment_id, Judges.judge_no, comment 
                    FROM 
                        {$wpdb->prefix}micerule_event_judges Judges 
                    LEFT JOIN 
                        {$wpdb->prefix}micerule_show_judges_general_comments Comments
                    ON 
                        Judges.event_post_id = Comments.event_post_id AND Judges.judge_no = Comments.judge_no
                    WHERE 
                        Judges.event_post_id = {$this->eventPostID}
                SQL;
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getJudgesClassData(): array|null{
        global $wpdb;
        $locationID = EventProperties::getEventLocationID($this->eventPostID);
         //TODO: split up Union Queries, DAOs
        $query = <<<SQL
                    WITH
                    ClassRegistrationCount AS (
                    SELECT 
                        INDICES.id AS count_index_id, COUNT(*) AS entry_count
                    FROM 
                        {$wpdb->prefix}micerule_show_classes_indices INDICES
                    INNER JOIN 
                        {$wpdb->prefix}micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                    INNER JOIN 
                        {$wpdb->prefix}micerule_show_user_registrations REGISTRATIONS ON REGISTRATIONS.class_id = INDICES.class_id AND REGISTRATIONS.age = INDICES.age
                    INNER JOIN 
                        {$wpdb->prefix}micerule_show_user_registrations_order REGISTRATIONS_ORDER ON REGISTRATIONS.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
                    GROUP BY INDICES.id
                    ),
                                            
                    SectionRegistrationCount AS (
                    SELECT INDICES.id AS count_index_id, COUNT(*) AS entry_count
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
                    )

                    SELECT ClassData.section, class_name, class_index, index_id, age, comment, comment_id, prize, judge_name, Judges.judge_no, COALESCE(entry_count, 0) AS entry_count
                    FROM(
                    SELECT location_id, CLASSES.section, class_name, class_index, INDICES.id as index_id, age, comment, COMMENTS.id as comment_id, "Class" as prize, entry_count
                    FROM {$wpdb->prefix}micerule_show_classes CLASSES
                    INNER JOIN {$wpdb->prefix}micerule_show_classes_indices INDICES ON CLASSES.id = INDICES.class_id
                    LEFT JOIN ClassRegistrationCount ON INDICES.id = ClassRegistrationCount.count_index_id
                    LEFT JOIN {$wpdb->prefix}micerule_show_judges_class_comments COMMENTS ON INDICES.id = COMMENTS.class_index_id
                    UNION
                    SELECT location_id, INDICES.section, challenge_name as class_name, challenge_index as class_index, INDICES.id as index_id, age, "" as comment, NULL as comment_id, "Section Challenge" as prize, entry_count
                    FROM sm1_micerule_show_challenges_indices INDICES
                    LEFT JOIN SectionRegistrationCount ON INDICES.id = SectionRegistrationCount.count_index_id
                    ) AS ClassData
                    INNER JOIN {$wpdb->prefix}micerule_event_judges_sections JudgesSections ON ClassData.section = JudgesSections.section
                    INNER JOIN {$wpdb->prefix}micerule_event_judges Judges ON JudgesSections.judge_no = Judges.judge_no AND JudgesSections.event_post_id = Judges.event_post_id
                    WHERE location_id = {$locationID} AND Judges.event_post_id = {$this->eventPostID}
                    ORDER BY class_index
                SQL;
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getGrandChallengeData(){
        global $wpdb;
        $locationID = EventProperties::getEventLocationID($this->eventPostID);
        $query = <<<SQL
                        SELECT location_id, INDICES.section, challenge_name as class_name, challenge_index as class_index, INDICES.id as index_id, GC_COUNT.age, "" as comment, NULL as comment_id, "Section Challenge" as prize, entry_count
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
                                    REGISTRATIONS.event_post_id = {$this->eventPostID}
                                GROUP BY 
                                    REGISTRATIONS.age
                            ) GC_COUNT 
                            ON GC_COUNT.age = INDICES.age
                            WHERE challenge_name = "GRAND CHALLENGE" AND INDICES.location_id = {$locationID}
                SQL;
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getJuniorData(){
        global $wpdb;
        $locationID = EventProperties::getEventLocationID($this->eventPostID);
        $query = <<<SQL
                    SELECT 
                    section,
                    class_name,
                    class_index,
                    ClassIndices.id as index_id,
                    age,
                    comment,
                    Comments.id as comment_id,
                    "Junior" as prize,
                    "" as judge_name,
                    NULL as judge_no,
                    (SELECT COUNT(*) 
                    FROM {$wpdb->prefix}micerule_show_user_registrations Registrations 
                    INNER JOIN {$wpdb->prefix}micerule_show_user_junior_registrations JRegistrations
                    ON Registrations.class_registration_id = JRegistrations.class_registration_id
                    WHERE event_post_id = {$this->eventPostID}
                    ) AS entry_count
                    FROM 
                        {$wpdb->prefix}micerule_show_classes_indices ClassIndices
                    INNER JOIN 
                        {$wpdb->prefix}micerule_show_classes Classes
                    ON 
                        ClassIndices.class_id = Classes.id
                    LEFT JOIN
                        {$wpdb->prefix}micerule_show_judges_class_comments Comments
                    ON
                        ClassIndices.id = Comments.class_index_id
                    WHERE 
                        class_name = "Junior" AND location_id = {$locationID}
                SQL;
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getJuniorPlacementReports(): array|null{
        global $wpdb;
        $query = <<<SQL
                    SELECT Reports.id, Placements.id as placement_id, class_index, Placements.placement, user_name, gender, class_name, Indices.age, comment
                    FROM sm1_micerule_show_class_placements Placements
                    INNER JOIN {$wpdb->prefix}micerule_show_entries Entries ON Placements.entry_id = Entries.id
                    INNER JOIN {$wpdb->prefix}micerule_show_user_registrations Registrations ON Entries.class_registration_id = Registrations.class_registration_id
                    INNER JOIN {$wpdb->prefix}micerule_show_classes_indices Indices ON Placements.index_id = Indices.id
                    INNER JOIN {$wpdb->prefix}micerule_show_classes Classes ON Indices.class_id = Classes.id
                    LEFT JOIN {$wpdb->prefix}micerule_show_judges_class_reports Reports ON Placements.id = Reports.placement_id
                    WHERE Registrations.event_post_id = {$this->eventPostID} AND prize = 1
                    ORDER BY Placements.placement
                    SQL;

        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getClassPlacementsReports(): array|null{
        global $wpdb;
        $query = <<<SQL
                    SELECT Reports.id, Placements.id as placement_id, class_index, Placements.placement, user_name, gender, class_name, variety_name, Indices.age, comment
                    FROM sm1_micerule_show_class_placements Placements
                    INNER JOIN {$wpdb->prefix}micerule_show_entries Entries ON Placements.entry_id = Entries.id
                    INNER JOIN {$wpdb->prefix}micerule_show_user_registrations Registrations ON Entries.class_registration_id = Registrations.class_registration_id
                    INNER JOIN {$wpdb->prefix}micerule_show_classes_indices Indices ON Placements.index_id = Indices.id
                    INNER JOIN {$wpdb->prefix}micerule_show_classes Classes ON Indices.class_id = Classes.id
                    LEFT JOIN {$wpdb->prefix}micerule_show_judges_class_reports Reports ON Placements.id = Reports.placement_id
                    WHERE Registrations.event_post_id = {$this->eventPostID}
                    ORDER BY class_index, Placements.placement
                    SQL;

        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getSectionPlacementReports(): array|null{
        global $wpdb;
        $query = <<<SQL
                    SELECT Placements.id, challenge_index, Placements.placement, user_name, class_name, Indices.age, variety_name
                    FROM {$wpdb->prefix}micerule_show_challenge_placements Placements
                    INNER JOIN {$wpdb->prefix}micerule_show_entries Entries ON Placements.entry_id = Entries.id
                    INNER JOIN {$wpdb->prefix}micerule_show_user_registrations Registrations ON Entries.class_registration_id = Registrations.class_registration_id
                    INNER JOIN {$wpdb->prefix}micerule_show_challenges_indices Indices ON Placements.index_id = Indices.id
                    INNER JOIN {$wpdb->prefix}micerule_show_classes Classes ON Registrations.class_id = Classes.id
                    WHERE Registrations.event_post_id = {$this->eventPostID}
                    ORDER BY challenge_index, Placements.placement
                    SQL;
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function submitClassComment(int|null $commentID, string|null $comment, int $classIndexID){
        global $wpdb;
        if(isset($commentID)){
            $wpdb->update($wpdb->prefix."micerule_show_judges_class_comments", array('comment' => $comment), array('id' => $commentID));
        }else{
            $wpdb->insert($wpdb->prefix."micerule_show_judges_class_comments", array('class_index_id' => $classIndexID, 'event_post_id' => $this->eventPostID, 'comment' => $comment));
        }
    }

    public function submitPlacementReport(int|null $reportID, int $classIndexID, int $placementID, string|null $gender, string|null $comment){
        global $wpdb;
        if(isset($reportID)){
            $wpdb->update($wpdb->prefix."micerule_show_judges_class_reports", array('comment' => $comment, 'gender' => $gender), array('id' => $reportID));
        }else{
            $wpdb->insert($wpdb->prefix."micerule_show_judges_class_reports", array('class_index_id' => $classIndexID, 'event_post_id' => $this->eventPostID, 'comment' => $comment, 'gender' => $gender, 'placement_id' => $placementID));
        }
    }

    public function submitGeneralComment(int|null $commentID, int $judgeNo, string|null $comment){
        global$wpdb;
        if(isset($commentID)){
            $wpdb->update($wpdb->prefix."micerule_show_judges_general_comments", array('comment' => $comment), array('id' => $commentID));
        }else{
            $wpdb->insert($wpdb->prefix."micerule_show_judges_general_comments", array('judge_no' => $judgeNo, 'event_post_id' => $this->eventPostID, 'comment' => $comment));
        }
    }
}