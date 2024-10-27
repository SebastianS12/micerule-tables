<?php

class JudgeModel extends Model{
    public int $event_post_id;
    public int $judge_no;
    public string $judge_name;

    private function __construct(int $event_post_id, int $judge_no, string $judg_name)
    {
        $this->event_post_id = $event_post_id;
        $this->judge_no = $judge_no;
        $this->judge_name = $judg_name;
    }

    public static function create(int $event_post_id, int $judge_no, string $judg_name): JudgeModel
    {
        return new self($event_post_id, $judge_no, $judg_name);
    }

    public static function createWithID(int $id, int $event_post_id, int $judge_no, string $judg_name): JudgeModel
    {
        $instance = self::create($event_post_id, $judge_no, $judg_name);
        $instance->id = $id;
        return $instance;
    }

    public function sections(): Collection
    {
        return $this->hasMany(JudgeSectionModel::class, Table::JUDGES_SECTIONS, "judge_id");
    }

    public function comment(): ?GeneralComment
    {
        return $this->hasOne(GeneralComment::class, Table::GENERAL_COMMENTS, "judge_id");
    }
}