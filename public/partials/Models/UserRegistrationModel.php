<?php

class UserRegistrationModel extends Model{
    public int $id;
    public int $eventPostID;
    public string $userName;
    public int $classIndexID;

    private function __construct(int $eventPostID, string $userName, int $classIndexID)
    {
        $this->eventPostID = $eventPostID;
        $this->userName = $userName;
        $this->classIndexID = $classIndexID;
    }

    public static function create(int $eventPostID, string $userName, int $classIndexID){
        return new self($eventPostID, $userName, $classIndexID);
    }

    public static function createWithID(int $id, int $eventPostID, string $userName, int $classIndexID){
        $instance = self::create($eventPostID, $userName, $classIndexID);
        $instance->id = $id;
        return $instance;
    }

    public function classIndex(): ClassIndexModel|null
    {
        return $this->belongsToOne(ClassIndexModel::class);
    }

    public function registrationOrder(): Collection
    {
        return $this->hasMany("order");
    }
}