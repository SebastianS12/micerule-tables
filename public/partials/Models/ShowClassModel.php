<?php

class ShowClassModel{
    //TODO: Missing Functions from ShowClassesModel.php 
    public $class_id;
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
        $this->class_id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."micerule_show_classes WHERE location_id = ".$locationID." AND class_name = '".$className."'");
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
    public int $class_index;
    public int $class_id;
    public string $age;

    private function __construct(int $class_index, int $class_id, string $age)
    {
        $this->class_index = $class_index;
        $this->class_id = $class_id;
        $this->age = $age;
    }

    public static function create(int $class_index, int $class_id, string $age){
        $instance = new self($class_index, $class_id, $age);
        return $instance;
    }

    public static function createWithID(int $id, int $class_index, int $class_id, string $age){
        $instance = self::create($class_index, $class_id, $age);
        $instance->id = $id;
        return $instance;
    }

    public function class(){
        return $this->belongsToOne(EntryClassModel::class, Table::CLASSES, "class_id");
    }

    public function showClass(): ?EntryClassModel
    {
        return $this->belongsToOne(EntryClassModel::class, Table::CLASSES, "class_id");
    }

    public function comment(): ?ClassComment
    {
        return $this->hasOne(ClassComment::class, Table::CLASS_COMMENTS, "class_index_id");
    }

    public function registrations(): Collection
    {
        return $this->hasMany(UserRegistrationModel::class, Table::REGISTRATIONS, "class_index_id");
    }

    public function placements(): Collection
    {
        return $this->hasMany(ClassPlacementModel::class, Table::CLASS_PLACEMENTS, "index_id");
    }
}