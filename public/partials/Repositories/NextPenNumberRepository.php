<?php

class NextPenNumberRepository implements IRepository{
    private int $locationID;

    public function __construct(int $locationID)
    {
        $this->locationID = $locationID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::NEXT_PENNUMBERS->getAlias().".*"])
                                ->from(Table::NEXT_PENNUMBERS)
                                ->join("INNER", Table::CLASS_INDICES, [Table::NEXT_PENNUMBERS], ["id"], ["class_index_id"])
                                ->join("INNER", Table::CLASSES, [Table::CLASS_INDICES], ["id"], ["class_id"])
                                ->where(Table::CLASSES->getAlias(), "location_id", "=", $this->locationID)
                                ->build();
                 
        global $wpdb;
        $nextPenNumbersQueryResult = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();
        foreach($nextPenNumbersQueryResult as $row){
            $nextPenNumberModel = NextPenNumberModel::createWithID(...$row);
            $collection->add($nextPenNumberModel);
        }

        return $collection;
    }

    public function save(NextPenNumberModel $nextPenNumberModel): void
    {
        global $wpdb;
        if(isset($nextPenNumberModel->id)){
            $wpdb->update($wpdb->prefix.Table::NEXT_PENNUMBERS->value, array('next_pen_number' => $nextPenNumberModel->next_pen_number), array('id' => $nextPenNumberModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::NEXT_PENNUMBERS->value, array('class_index_id' => $nextPenNumberModel->class_index_id, 'next_pen_number' => $nextPenNumberModel->next_pen_number));
        }
    }
}