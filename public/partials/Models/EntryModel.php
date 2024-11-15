<?php

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