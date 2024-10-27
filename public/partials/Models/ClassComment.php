<?php

class ClassComment extends Model{
    public int $event_post_id;
    public int $class_index_id;
    public string $comment;

    private function __construct(int $event_post_id, int $class_index_id, ?string $comment)
    {
        $this->event_post_id = $event_post_id;
        $this->class_index_id = $class_index_id;
        $this->comment = $comment;
    }

    public static function create(int $event_post_id, int $class_index_id, ?string $comment): ClassComment
    {
        return new self($event_post_id, $class_index_id, $comment);
    }

    public static function createWithID(int $id, int $event_post_id, int $class_index_id, ?string $comment): ClassComment
    {
        $instance = self::create($event_post_id, $class_index_id, $comment);
        $instance->id = $id;
        return $instance;
    }
}