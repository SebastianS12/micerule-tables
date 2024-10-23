<?php

class PlacementReportsRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::CLASS_REPORTS->getAlias().".*"])
                                ->from(Table::CLASS_REPORTS)
                                ->where(Table::CLASS_REPORTS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();

        global $wpdb;
        $placementReportsQueryResults = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();
        foreach($placementReportsQueryResults as $row){
            $placementReportModel = PlacementReport::createWithID($row['id'], $row['event_post_id'], $row['class_index_id'], $row['gender'], $row['comment'], $row['placement_id']);
            $collection->add($placementReportModel);
        }

        return $collection;
    }

    public function save(PlacementReport $placementReportModel): void
    {
        global $wpdb;
        if(isset($placementReportModel->id)){
            $wpdb->update($wpdb->prefix.Table::CLASS_REPORTS->value, array('comment' => $placementReportModel->comment, 'gender' => $placementReportModel->gender), array('id' => $placementReportModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::CLASS_REPORTS->value, array('class_index_id' => $placementReportModel->classIndexID, 'event_post_id' => $placementReportModel->eventPostID, 'comment' => $placementReportModel->comment, 'gender' => $placementReportModel->gender, 'placement_id' => $placementReportModel->placementID));
        }
    }
}