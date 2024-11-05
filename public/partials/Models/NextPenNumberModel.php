<?php

class NextPenNumberModel extends Model{
    public int $class_index_id;
    public int $next_pen_number;

    private function __construct(int $class_index_id, int $next_pen_number)
    {
        $this->class_index_id = $class_index_id;
        $this->next_pen_number = $next_pen_number;
    }

    public static function create(int $class_index_id, int $next_pen_number): NextPenNumberModel
    {
        return new self($class_index_id, $next_pen_number);
    }

    public static function createWithID(int $id, int $class_index_id, int $next_pen_number): NextPenNumberModel
    {
        $instance = self::create($class_index_id, $next_pen_number);
        $instance->id = $id;
        return $instance;
    }
}