<?php

class ClassComment extends Model{
    public int $eventPostID;
    public int $classIndexID;
    public string $comment;

    private function __construct(int $eventPostID, int $classIndexID, ?string $comment)
    {
        $this->eventPostID = $eventPostID;
        $this->classIndexID = $classIndexID;
        $this->comment = $comment;
    }

    public static function create(int $eventPostID, int $classIndexID, ?string $comment): ClassComment
    {
        return new self($eventPostID, $classIndexID, $comment);
    }

    public static function createWithID(int $id, int $eventPostID, int $classIndexID, ?string $comment): ClassComment
    {
        $instance = self::create($eventPostID, $classIndexID, $comment);
        $instance->id = $id;
        return $instance;
    }
}