<?php

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

    public function nextPenNumber(): ?NextPenNumberModel
    {
        return $this->hasOne(NextPenNumberModel::class, Table::NEXT_PENNUMBERS, "class_index_id");
    }
}