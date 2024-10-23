<?php

class GeneralCommentRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::GENERAL_COMMENTS->getAlias().".*"])
                                ->from(Table::JUDGES)
                                ->join("INNER", Table::GENERAL_COMMENTS, [Table::JUDGES], ["judge_id"], ["id"])
                                ->where(Table::JUDGES->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();

        global $wpdb;
        $generalCommentsQueryResults = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();

        foreach($generalCommentsQueryResults as $row){
            $generalCommentModel = GeneralComment::createWithID($row['id'], $row['judge_id'], $row['comment']);
            $collection->add($generalCommentModel);
        }

        return $collection;
    }

    public function save(GeneralComment $generalCommentModel): void
    {
        global $wpdb;
        if(isset($generalCommentModel->id)){
            $wpdb->update($wpdb->prefix.Table::GENERAL_COMMENTS->value, array('comment' => $generalCommentModel->comment), array('id' => $generalCommentModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::GENERAL_COMMENTS->value, array('comment' => $generalCommentModel->comment, 'judge_id' => $generalCommentModel->judgeID));
        }
    }
}