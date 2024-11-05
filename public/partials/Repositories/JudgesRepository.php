<?php

class JudgesRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::JUDGES)
                                ->where(Table::JUDGES->getAlias(), "event_post_id", "=", $this->eventPostID);

        if(isset($constraintsClosure)){
            $constraintsClosure($query);
        }

        global $wpdb;
        $judgesQueryResult = $wpdb->get_results($query->build(), ARRAY_A);

        $collection = new Collection();
        foreach($judgesQueryResult as $row){
            $judgeModel = JudgeModel::createWithID($row['id'], $row['event_post_id'], $row['judge_no'], $row['judge_name']);
            $collection->add($judgeModel);
        }


        return $collection;
    }

    public function save(JudgeModel $judgeModel): int
    {
        global $wpdb;
        if(isset($judgeModel->id)){
            $wpdb->update($wpdb->prefix.Table::JUDGES->value, array('event_post_id' => $judgeModel->event_post_id, 'judge_no' => $judgeModel->judge_no, 'judge_name' => $judgeModel->judge_name), array('id' => $judgeModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::JUDGES->value, array('event_post_id' => $judgeModel->event_post_id, 'judge_no' => $judgeModel->judge_no, 'judge_name' => $judgeModel->judge_name));
            $judgeModel->id = $wpdb->insert_id;
        }

        return $judgeModel->id;
    }

    public function remove(JudgeModel $judgeModel): void
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix.Table::JUDGES->value, array('id' => $judgeModel->id));
    }
    // public function getAll(int $eventPostID): ?array{
    //     global $wpdb;
    //     $query = <<<SQL
    //                 SELECT
    //                     *
    //                 FROM
    //                     {$wpdb->prefix}micerule_event_judges Judges
    //                 INNER JOIN
    //                     {$wpdb->prefix}micerule_event_judges_sections JudgesSections
    //                 ON 
    //                     Judges.judge_no = JudgesSections.judge_no AND Judges.event_post_id = JudgesSections.event_post_id
    //                 WHERE
    //                     Judges.event_post_id = {$eventPostID}
    //                 SQL;
    //     return $wpdb->get_results($query, ARRAY_A);
    // }
}