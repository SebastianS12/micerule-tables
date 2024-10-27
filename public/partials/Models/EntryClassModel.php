<?php

class EntryClassModel extends Model{
    public int $id;
    public $location_id;
    public $class_name;
    public $section;
    public $section_position;

    private function __construct($location_id, $class_name, $section, $section_position)
    {
        $this->location_id = $location_id;
        $this->class_name = $class_name;
        $this->section = $section;
        $this->section_position = $section_position;
    }

    public static function create($location_id, $class_name, $section, $section_position): EntryClassModel{
        return new self($location_id, $class_name, $section, $section_position);
    }

    public static function createWithID($id, $location_id, $class_name, $section, $section_position): EntryClassModel{
        $instance = self::create($location_id, $class_name, $section, $section_position);
        $instance->id = $id;
        return $instance;
    }

    public function classIndices(): Collection
    {
        return $this->hasMany(ClassIndexModel::class, Table::CLASS_INDICES, "class_id");
    }

    public function judgeSection(): ?JudgeSectionModel
    {
        return $this->hasOne(JudgeSectionModel::class, Table::JUDGES_SECTIONS, "section");
    }
}