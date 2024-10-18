<?php

class ClassIndexRepository implements IRepository{
    private $locationID;
    public function __construct($locationID){
        $this->locationID = $locationID;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection
    {
        global $wpdb;
        $classIndexQueryData = $wpdb->get_results("SELECT CI.id, class_id, age, class_index 
                                          FROM ".$wpdb->prefix."micerule_show_classes_indices CI
                                          INNER JOIN ".$wpdb->prefix."micerule_show_classes C
                                          ON CI.class_id = C.id
                                          WHERE location_id = ".$this->locationID, ARRAY_A);

        $collection = new Collection();
        foreach($classIndexQueryData as $classIndexData){
            $classIndexModel = ClassIndexModel::createWithID($classIndexData['id'], $classIndexData['class_index'], $classIndexData['class_id'], $classIndexData['age']);
            $collection->add($classIndexModel);
        }

        return $collection;
    }

    public function getJuniorIndexModel(): ClassIndexModel|null
    {
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::CLASS_INDICES)
                                ->join("INNER", Table::CLASSES, [Table::CLASS_INDICES], ["id"], ["class_id"])
                                ->where(Table::CLASSES->getAlias(), "location_id", "=", $this->locationID)
                                ->where(Table::CLASSES->getAlias(), "class_name", "=", "Junior")
                                ->where(Table::CLASS_INDICES->getAlias(), "age", "=", "AA")
                                ->build();

        global $wpdb;
        $juniorRow = $wpdb->get_row($query, ARRAY_A);

        if(!isset($juniorRow)) return null;
        return ClassIndexModel::createWithID($juniorRow['id'], $juniorRow['class_index'], $juniorRow['class_id'], $juniorRow['age']);
    }

    public function getClassIndexModel($className, $age){
        global $wpdb;
        $classIndexData = $wpdb->get_row("SELECT CI.id, class_id, age, class_index 
                                          FROM ".$wpdb->prefix."micerule_show_classes_indices CI
                                          INNER JOIN ".$wpdb->prefix."micerule_show_classes C
                                          ON CI.class_id = C.id
                                          WHERE location_id = ".$this->locationID." AND class_name = '".$className."' AND age = '".$age."'", ARRAY_A);

        return ClassIndexModel::createWithID($classIndexData['id'], $classIndexData['class_index'], $classIndexData['class_id'], $classIndexData['age']);
    }
}