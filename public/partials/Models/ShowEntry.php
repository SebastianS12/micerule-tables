<?php

class ShowEntry{
    public $ID;
    public $pen_number;
    public $userName;
    public $className;
    public $sectionName;
    public $age;
    public $variety_name;
    public $absent;
    public $added;
    public $moved;

    private function __construct(){}

    public static function createWithPenNumber($eventPostID, $pen_number){
        $showEntry = new ShowEntry();
        $showEntry->pen_number = $pen_number;
        $entryData = self::getEntryDataWithPenNumber($eventPostID, $pen_number);
        $showEntry->loadEntryData($entryData);

        return $showEntry;
    }

    public static function createWithClassRegistration($pen_number, $classRegistrationID, $classRegistrationOrder){
        $showEntry = new ShowEntry();
        $entryData = self::getEntryDataWithClassRegistration($classRegistrationID, $classRegistrationOrder);
        $showEntry->loadEntryData($entryData);
        $showEntry->pen_number = $pen_number;

        return $showEntry;
    }

    public static function createWithEntryID($entryID){
        $showEntry = new ShowEntry();
        $entryData = self::getEntryDatawithEntryID($entryID);
        $showEntry->loadEntryData($entryData);

        return $showEntry;
    }

    private static function getEntryDatawithPenNumber($eventPostID, $pen_number){
        global $wpdb;
        return $wpdb->get_row("SELECT ENTRIES.id, pen_number, variety_name, absent, moved, added, user_name, class_name, section, age FROM ".$wpdb->prefix."micerule_show_entries ENTRIES 
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id 
                               WHERE event_post_id = ".$eventPostID." AND pen_number = ".$pen_number, ARRAY_A);
    }

    private static function getEntryDataWithClassRegistration($classRegistrationID, $classRegistrationOrder){
        global $wpdb;
        return $wpdb->get_row("SELECT ENTRIES.id, pen_number, variety_name, absent, moved, added, user_name, class_name, section, age FROM ".$wpdb->prefix."micerule_show_entries ENTRIES 
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations_order REGISTRATIONS_ORDER ON ENTRIES.class_registration_id = REGISTRATIONS_ORDER.class_registration_id
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                               WHERE REGISTRATIONS.class_registration_id = ".$classRegistrationID." AND ENTRIES.registration_order = ".$classRegistrationOrder."", ARRAY_A);
    }

    private static function getEntryDatawithEntryID($entryID){
        global $wpdb;
        return $wpdb->get_row("SELECT ENTRIES.id, pen_number, variety_name, absent, moved, added, user_name, class_name, section, age FROM ".$wpdb->prefix."micerule_show_entries ENTRIES 
                               INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id 
                               INNER JOIN ".$wpdb->prefix."micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id  
                               WHERE ENTRIES.id = ".$entryID, ARRAY_A);
    }

    private function loadEntryData($entryData){
        if($entryData != null){
            $this->ID = $entryData['id'];
            $this->pen_number = $entryData['pen_number'];
            $this->userName = $entryData['user_name'];
            $this->className = $entryData['class_name'];
            $this->sectionName = $entryData['section'];
            $this->age = $entryData['age'];
            $this->variety_name = $entryData['variety_name'];
            $this->absent = $entryData['absent'];
            $this->added = $entryData['added'];
            $this->moved = $entryData['moved'];
        } 
    }

    public function getRegistrationOrder(){
        global $wpdb;
        return $wpdb->get_var("SELECT registration_order FROM ".$wpdb->prefix."micerule_show_entries WHERE id = ".$this->ID);
    }

    public function save($classRegistrationID, $registrationOrder, $variety_name, $added, $moved){
        global $wpdb;
        if($this->ID == null)
            $wpdb->insert($wpdb->prefix."micerule_show_entries", array("class_registration_id" => $classRegistrationID, "registration_order" => $registrationOrder, "pen_number" => $this->pen_number, "variety_name" => $variety_name, "added" => $added, "moved" => $moved));
        else
            $wpdb->update($wpdb->prefix."micerule_show_entries", array("pen_number" => $this->pen_number, "variety_name" => $variety_name), array("id" => $this->ID));
    }

    public function delete(){
        global $wpdb;
        $wpdb->delete($wpdb->prefix."micerule_show_entries", array("id" => $this->ID));
    }

    public function editAbsent($isAbsent){
        global $wpdb;
        $wpdb->update($wpdb->prefix."micerule_show_entries", array("absent" => $isAbsent), array("id" => $this->ID));
    }

    public function editVarietyName($variety_name){
        global $wpdb;
        $wpdb->update($wpdb->prefix."micerule_show_entries", array("variety_name" => $variety_name), array("id" => $this->ID));
    }
}


class EntryModel extends Model{
    public int $registration_order_id;
    public int $pen_number;
    public string $variety_name;
    public bool $absent;
    public bool $added;
    public bool $moved;

    private function __construct(int $registration_order_id, int $pen_number, string $variety_name, bool $absent, bool $added, bool $moved)
    {
        $this->registration_order_id = $registration_order_id;
        $this->pen_number = $pen_number;
        $this->variety_name = $variety_name;
        $this->absent = $absent;
        $this->added = $added;
        $this->moved = $moved;
    }

    public static function create(int $registration_order_id, int $pen_number, string $variety_name, bool $absent, bool $added, bool $moved){
        $instance = new self($registration_order_id, $pen_number, $variety_name, $absent, $added, $moved);
        return $instance;
    }

    public static function createWithID(int $id, int $registration_order_id, int $pen_number, string $variety_name, bool $absent, bool $added, bool $moved){
        $instance = self::create($registration_order_id, $pen_number, $variety_name, $absent, $added, $moved);
        $instance->id = $id;
        return $instance;
    }

    public function placements(): Collection
    {
        return $this->hasMany(ClassPlacementModel::class, Table::CLASS_PLACEMENTS, "entry_id")->concat($this->hasMany(ChallengePlacementModel::class, Table::CHALLENGE_PLACEMENTS, "entry_id"));
    }

    public function showClass(): ?EntryClassModel
    {
        $relations = [RegistrationOrderModel::class, UserRegistrationModel::class, ClassIndexModel::class, EntryClassModel::class];
        $relationTables = [Table::REGISTRATIONS_ORDER, Table::REGISTRATIONS, Table::CLASS_INDICES, Table::CLASSES];
        $foreignKeys = ["registration_order_id", "registration_id", "class_index_id", "class_id"];
        return $this->belongsToOneThrough($relations, $relationTables, $foreignKeys);
    }

    public function registrationOrder(): ?RegistrationOrderModel
    {
        return $this->belongsToOne(RegistrationOrderModel::class, Table::REGISTRATIONS_ORDER, "registration_order_id");
    }
}