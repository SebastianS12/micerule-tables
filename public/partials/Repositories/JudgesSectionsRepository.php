<?php

class JudgesSectionsRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::JUDGES_SECTIONS->getAlias().".*"])
                                ->from(Table::JUDGES_SECTIONS)
                                ->join("INNER", Table::JUDGES, [Table::JUDGES_SECTIONS], ["id"], ["judge_id"])
                                ->where(Table::JUDGES->getAlias(), "event_post_id", "=", $this->eventPostID);

        if(isset($constraintsClosure)){
            $constraintsClosure($query);
        }

        global $wpdb;
        $judgesSectionsQueryResult = $wpdb->get_results($query->build(), ARRAY_A);

        $collection = new Collection();
        foreach($judgesSectionsQueryResult as $row){
            $judgeSectionModel = JudgeSectionModel::createWithID($row['id'], $row['judge_id'], $row['section']);
            $collection->add($judgeSectionModel);
        }

        return $collection;
    }

    public function save(JudgeSectionModel $judgeSectionModel): int
    {
        global $wpdb;
        if(isset($judgeSectionModel->id)){
            $wpdb->update($wpdb->prefix.Table::JUDGES_SECTIONS->value, array('section' => $judgeSectionModel->section, 'judge_id' => $judgeSectionModel->judge_id), array('id' => $judgeSectionModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::JUDGES_SECTIONS->value, array('section' => $judgeSectionModel->section, 'judge_id' => $judgeSectionModel->judge_id));
            $judgeSectionModel->id = $wpdb->insert_id;
        }

        return $judgeSectionModel->id;
    }

    public function remove(JudgeSectionModel $judgeSectionModel): void
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix.Table::JUDGES_SECTIONS->value, array('id' => $judgeSectionModel->id));
    }
}