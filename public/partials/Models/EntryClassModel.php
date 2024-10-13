<?php

class EntryClassModel extends Model{
    public int $id;
    public $locationID;
    public $className;
    public $sectionName;
    public $sectionPosition;

    private function __construct($locationID, $className, $sectionName, $sectionPosition)
    {
        $this->locationID = $locationID;
        $this->className = $className;
        $this->sectionName = $sectionName;
        $this->sectionPosition = $sectionPosition;
    }

    public static function create($locationID, $className, $sectionName, $sectionPosition): EntryClassModel{
        return new self($locationID, $className, $sectionName, $sectionPosition);
    }

    public static function createWithID($id, $locationID, $className, $sectionName, $sectionPosition): EntryClassModel{
        $instance = self::create($locationID, $className, $sectionName, $sectionPosition);
        $instance->id = $id;
        return $instance;
    }
}