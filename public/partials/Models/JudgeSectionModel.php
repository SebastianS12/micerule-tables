<?php

class JudgeSectionModel extends Model{
    public int $judgeID;
    public string $section;

    private function __construct(int $judgeID, string $section)
    {
        $this->judgeID = $judgeID;
        $this->section = $section;
    }

    public static function create(int $judgeID, string $section): JudgeSectionModel
    {
        return new self($judgeID, $section);
    }

    public static function createWithID(int $id, int $judgeID, string $section): JudgeSectionModel
    {
        $instance = self::create($judgeID, $section);
        $instance->id = $id;
        return $instance;
    }

    public function judge(): ?JudgeModel
    {
        return $this->belongsToOne(JudgeModel::class);
    }

}