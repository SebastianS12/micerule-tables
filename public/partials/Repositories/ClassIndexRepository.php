<?php

class ClassIndexRepository implements IRepository{
    private $locationID;
    public function __construct($locationID){
        $this->locationID = $locationID;
    }

    public function getAll(): Collection
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