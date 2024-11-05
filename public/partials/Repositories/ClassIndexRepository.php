<?php

class ClassIndexRepository implements IRepository{
    private $locationID;
    public function __construct($locationID){
        $this->locationID = $locationID;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::CLASS_INDICES->getAlias().".*"])
                                ->from(Table::CLASS_INDICES)
                                ->join("INNER", Table::CLASSES, [Table::CLASS_INDICES], ["id"], ["class_id"])
                                ->where(Table::CLASSES->getAlias(), "location_id", "=", $this->locationID)
                                ->orderBy(Table::CLASS_INDICES->getAlias(), "class_index")
                                ->build();

        global $wpdb;
        $classIndexQueryData = $wpdb->get_results($query, ARRAY_A);

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

    public function getByID(int $classIndexID): ?ClassIndexModel
    {
        global $wpdb;
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::CLASS_INDICES)
                                ->where(Table::CLASS_INDICES->getAlias(), "id", "=", $classIndexID)
                                ->limit(1)
                                ->build();

        $classIndexModelData = $wpdb->get_row($query, ARRAY_A);
        if(!isset($classIndexModelData)) return null;

        return ClassIndexModel::createWithID(...$classIndexModelData);
    }

    public function save(ClassIndexModel $classIndexModel): void
    {
        global $wpdb;
        if(isset($classIndexModel->id)){
            $wpdb->update($wpdb->prefix.Table::CLASS_INDICES->value, array('class_index' => $classIndexModel->class_index, 'class_id' => $classIndexModel->class_id, 'age' => $classIndexModel->age), array('id' => $classIndexModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::CLASS_INDICES->value, array('class_index' => $classIndexModel->class_index, 'class_id' => $classIndexModel->class_id, 'age' => $classIndexModel->age));
        }
    }
}