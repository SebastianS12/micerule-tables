<?php

class ClassCommentsRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::CLASS_COMMENTS->getAlias().".*"])
                                ->from(Table::CLASS_COMMENTS)
                                ->where(Table::CLASS_COMMENTS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();

        global $wpdb;
        $classCommentsQueryResult = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();
        foreach($classCommentsQueryResult as $row){
            $classCommentModel = ClassComment::createWithID($row['id'], $row['event_post_id'], $row['class_index_id'], $row['comment']);
            $collection->add($classCommentModel);
        }

        return $collection;
    }

    public function save(ClassComment $classCommentModel): void
    {
        global $wpdb;
        if(isset($classCommentModel->id)){
            $wpdb->update($wpdb->prefix.Table::CLASS_COMMENTS->value, array('comment' => $classCommentModel->comment), array('id' => $classCommentModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::CLASS_COMMENTS->value, array('class_index_id' => $classCommentModel->classIndexID, 'event_post_id' => $classCommentModel->eventPostID, 'comment' => $classCommentModel->comment));
        }
    }
}