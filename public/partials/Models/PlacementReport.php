<?php

class PlacementReport extends Model{
    public int $eventPostID;
    public int $classIndexID;
    public string $gender;
    public string $comment;
    public int $placementID;

    private function __construct(int $eventPostID, int $classIndexID, string $gender, string $comment, int $placementID)
    {
        $this->eventPostID = $eventPostID;
        $this->classIndexID = $classIndexID;
        $this->gender = $gender;
        $this->comment = $comment;
        $this->placementID = $placementID;
    }

    public static function create(int $eventPostID, int $classIndexID, string $gender, string $comment, int $placementID): PlacementReport
    {
        return new self($eventPostID, $classIndexID, $gender, $comment, $placementID);
    }

    public static function createWithID(int $id, int $eventPostID, int $classIndexID, string $gender, string $comment, int $placementID): PlacementReport
    {
        $instance = self::create($eventPostID, $classIndexID, $gender, $comment, $placementID);
        $instance->id = $id;
        return $instance;
    }
}