<?php

class GeneralComment extends Model{
    public int $judgeID;
    public string $comment;

    private function __construct(int $judgeID, string $comment)
    {
        $this->judgeID = $judgeID;
        $this->comment = $comment;
    }

    public static function create(int $judgeID, string $comment): GeneralComment
    {
        return new self($judgeID, $comment);
    }    

    public static function createWithID(int $id, int $judgeID, string $comment): GeneralComment
    {
        $instance = self::create($judgeID, $comment);
        $instance->id = $id;
        return $instance;
    }
}