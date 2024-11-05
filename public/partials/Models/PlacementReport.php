<?php

class PlacementReport extends Model{
    public int $event_post_id;
    public int $class_index_id;
    public string $gender;
    public string $comment;
    public int $placement_id;

    private function __construct(int $event_post_id, int $class_index_id, string $gender, string $comment, int $placement_id)
    {
        $this->event_post_id = $event_post_id;
        $this->class_index_id = $class_index_id;
        $this->gender = $gender;
        $this->comment = $comment;
        $this->placement_id = $placement_id;
    }

    public static function create(int $event_post_id, int $class_index_id, string $gender, string $comment, int $placement_id): PlacementReport
    {
        return new self($event_post_id, $class_index_id, $gender, $comment, $placement_id);
    }

    public static function createWithID(int $id, int $event_post_id, int $class_index_id, string $gender, string $comment, int $placement_id): PlacementReport
    {
        $instance = self::create($event_post_id, $class_index_id, $gender, $comment, $placement_id);
        $instance->id = $id;
        return $instance;
    }
}