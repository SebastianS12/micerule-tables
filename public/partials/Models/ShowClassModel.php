<?php

class ShowClassModel{
    //TODO: Missing Functions from ShowClassesModel.php 
    public $classID;
    public $name;
    public $index;
    public $age;
    public $penNumbers;

    public function __construct($eventPostID, $className, $age)
    {
        $this->loadClassData($eventPostID, $className, $age);
    }

    private function loadClassData($eventPostID, $className, $age){
        global $wpdb;
        $locationID = EventProperties::getEventLocationID($eventPostID);
        $this->classID = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_classes WHERE location_id = ".$locationID." AND class_name = '".$className."'");
        $this->name = $className;
        $this->index = $wpdb->get_var("SELECT class_index FROM ".$wpdb->prefix."micerule_show_classes_indices INDICES
                                       INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON INDICES.class_id = CLASSES.id
                                       WHERE location_id = '".$locationID."' AND class_name = '".$className."' AND age = '".$age."'");
        $this->age = $age;
        $this->penNumbers = $wpdb->get_col("SELECT pen_number FROM ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS 
                                            INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations_order REG_ORDER ON REGISTRATIONS.class_registration_id = REG_ORDER.class_registration_id 
                                            INNER JOIN ".$wpdb->prefix."micerule_show_entries PENNUMBERS ON REG_ORDER.class_registration_id = PENNUMBERS.class_registration_id AND REG_ORDER.registration_order = PENNUMBERS.registration_order
                                            INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id 
                                            WHERE event_post_id = ".$eventPostID." AND class_name = '".$className."' AND age = '".$age."'
                                            ORDER BY pen_number");
        //TODO: Enum
        if($className == "Junior")
            $this->penNumbers = $this->getJuniorPenNumbers($eventPostID);
    }

    private function getJuniorPenNumbers($eventPostID){
        global $wpdb;
        return $wpdb->get_col("SELECT pen_number FROM ".$wpdb->prefix."micerule_show_user_junior_registrations JUNIOR_REGISTRATIONS 
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON REGISTRATIONS.class_registration_id = JUNIOR_REGISTRATIONS.class_registration_id 
                               INNER JOIN ".$wpdb->prefix."micerule_show_entries PENNUMBERS ON JUNIOR_REGISTRATIONS.class_registration_id = PENNUMBERS.class_registration_id AND JUNIOR_REGISTRATIONS.registration_order = PENNUMBERS.registration_order
                               WHERE event_post_id = ".$eventPostID." ORDER BY pen_number");
    }
}


class ClassIndexModel extends Model{
    public int $id;
    public $index;
    public $classID;
    public $age;
    private $indexTable;

    private function __construct($index, $classID, $age)
    {
        $this->index = $index;
        $this->classID = $classID;
        $this->age = $age;

        global $wpdb;
        $this->indexTable = $wpdb->prefix."micerule_show_classes_indices";
    }

    public static function create($index, $classID, $age){
        $instance = new self($index, $classID, $age);
        return $instance;
    }

    public static function createWithID($id, $index, $classID, $age){
        $instance = self::create($index, $classID, $age);
        $instance->id = $id;
        return $instance;
    }

    public function class(){
        return $this->hasOne("class");
    }

    public function save(){
        global $wpdb;
        if($this->id){
            $wpdb->update($this->indexTable, $this->getValues(), array('id' => $this->id));
        }else{
            $wpdb->insert($this->indexTable, $this->getValues());
        }
    }

    private function getValues(){
        return array('class_id' => $this->classID, 'age' => $this->age, 'class_index' => $this->index);
    }

    public function delete(){
        global $wpdb;
        $wpdb->delete($this->indexTable, array('id' => $this->id));
    }
}