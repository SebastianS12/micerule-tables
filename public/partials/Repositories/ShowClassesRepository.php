<?php

class ShowClassesRepository implements IRepository{
    public $locationID;

    public function __construct($locationID)
    {
        $this->locationID = $locationID;
    }

    public function getAll(?Closure $constraintsClosure = null): Collection{
        global $wpdb;
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::CLASSES)
                                ->where(Table::CLASSES->getAlias(), "location_id", "=", $this->locationID)
                                ->orderByField("section", ["selfs", "tans", "marked", "satins", "aovs", "optional"])
                                ->orderBy(Table::CLASSES->getAlias(), "section_position");

        if(isset($constraintsClosure)){
            $constraintsClosure($query);
        }

        $showClassesQueryResults = $wpdb->get_results($query->build(), ARRAY_A);

        $collection = new Collection();
        foreach($showClassesQueryResults as $row){
            $showClassModel = EntryClassModel::createWithID($row['id'], $row['location_id'], $row['class_name'], $row['section'], $row['section_position']);
            $collection->add($showClassModel);
        }

        return $collection;
    }

    public function getByID($id): EntryClassModel
    {
        global $wpdb;
        $classData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_show_classes
                                     WHERE id = ".$id, ARRAY_A);

        if($classData['id'] == null)
            return null;

        return EntryClassModel::createWithID($classData['id'], $classData['location_id'], $classData['class_name'], $classData['section'], $classData['section_position']);
    }

    public function save(EntryClassModel $entryClassModel): void
    {
        global $wpdb;
        if(isset($entryClassModel->id)){
            $wpdb->update($wpdb->prefix.Table::CLASSES->value, array('location_id' => $entryClassModel->location_id, 'class_name' => $entryClassModel->class_name, 'section' => $entryClassModel->section, 'section_position' => $entryClassModel->section_position), array('id' => $entryClassModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::CLASSES->value, array('location_id' => $entryClassModel->location_id, 'class_name' => $entryClassModel->class_name, 'section' => $entryClassModel->section, 'section_position' => $entryClassModel->section_position));
        }
    }

    public function delete(int $classID): void
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix.Table::CLASSES->value, array('id' => $classID));
    }
}