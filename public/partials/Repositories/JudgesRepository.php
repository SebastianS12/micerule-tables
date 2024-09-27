<?php

class JudgesRepository{

    public function getAll(int $eventPostID): ?array{
        global $wpdb;
        $query = <<<SQL
                    SELECT
                        *
                    FROM
                        {$wpdb->prefix}micerule_event_judges Judges
                    INNER JOIN
                        {$wpdb->prefix}micerule_event_judges_sections JudgesSections
                    ON 
                        Judges.judge_no = JudgesSections.judge_no AND Judges.event_post_id = JudgesSections.event_post_id
                    WHERE
                        Judges.event_post_id = {$eventPostID}
                    SQL;
        return $wpdb->get_results($query, ARRAY_A);
    }
}