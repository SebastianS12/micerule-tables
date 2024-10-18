<?php

class ShowClassesRepository implements IRepository{
    public $locationID;

    public function __construct($locationID)
    {
        $this->locationID = $locationID;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection{
        global $wpdb;
        $classesData = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_show_classes
                                           WHERE location_id = ".$this->locationID, ARRAY_A);

        $collection = new Collection();
        foreach($classesData as $row){
            $showClassModel = EntryClassModel::createWithID($row['id'], $row['location_id'], $row['class_name'], $row['section'], $row['section_position']);
            $collection->add($showClassModel);
        }

        return $collection;
    }

    public function getByID($id): EntryClassModel{
        global $wpdb;
        $classData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_show_classes
                                     WHERE id = ".$id, ARRAY_A);

        if($classData['id'] == null)
            return null;

        return EntryClassModel::createWithID($classData['id'], $classData['location_id'], $classData['class_name'], $classData['section'], $classData['section_position']);
    }
}