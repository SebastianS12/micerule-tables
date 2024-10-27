<?php

class JudgeSectionModel extends Model{
    public int $judge_id;
    public string $section;

    private function __construct(int $judge_id, string $section)
    {
        $this->judge_id = $judge_id;
        $this->section = $section;
    }

    public static function create(int $judge_id, string $section): JudgeSectionModel
    {
        return new self($judge_id, $section);
    }

    public static function createWithID(int $id, int $judge_id, string $section): JudgeSectionModel
    {
        $instance = self::create($judge_id, $section);
        $instance->id = $id;
        return $instance;
    }

    public function judge(): ?JudgeModel
    {
        return $this->belongsToOne(JudgeModel::class, Table::JUDGES, "judge_id");
    }

}