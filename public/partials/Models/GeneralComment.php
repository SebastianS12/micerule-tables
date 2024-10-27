<?php

class GeneralComment extends Model{
    public int $judge_id;
    public string $comment;

    private function __construct(int $judge_id, string $comment)
    {
        $this->judge_id = $judge_id;
        $this->comment = $comment;
    }

    public static function create(int $judge_id, string $comment): GeneralComment
    {
        return new self($judge_id, $comment);
    }    

    public static function createWithID(int $id, int $judge_id, string $comment): GeneralComment
    {
        $instance = self::create($judge_id, $comment);
        $instance->id = $id;
        return $instance;
    }
}