<?php

class JudgeModel extends Model{
    public int $eventPostID;
    public int $judgeNo;
    public string $judgeName;

    private function __construct(int $eventPostID, int $judgeNo, string $judgeName)
    {
        $this->eventPostID = $eventPostID;
        $this->judgeNo = $judgeNo;
        $this->judgeName = $judgeName;
    }

    public static function create(int $eventPostID, int $judgeNo, string $judgeName): JudgeModel
    {
        return new self($eventPostID, $judgeNo, $judgeName);
    }

    public static function createWithID(int $id, int $eventPostID, int $judgeNo, string $judgeName): JudgeModel
    {
        $instance = self::create($eventPostID, $judgeNo, $judgeName);
        $instance->id = $id;
        return $instance;
    }

    public function sections(): Collection
    {
        return $this->hasMany("sections");
    }

    public function comment(): ?GeneralComment
    {
        return $this->hasOne("comment");
    }
}