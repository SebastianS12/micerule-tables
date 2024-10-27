<?php

class AwardModel extends Model{
    public int $challenge_placement_id;
    public Award $award;
    public bool $printed;
    public Prize $prize;

    private function __construct(int $challenge_placement_id, string $award, bool $printed, int $prize)
    {
        $this->challenge_placement_id = $challenge_placement_id;
        $this->award = Award::from($award);
        $this->printed = $printed;
        $this->prize = Prize::from($prize);
    }

    public static function create(int $challenge_placement_id, string $award, bool $printed, int $prize): AwardModel
    {
        return new self($challenge_placement_id, $award, $printed, $prize);
    }

    public static function createWithID(int $id, int $challenge_placement_id, string $award, bool $printed, int $prize): AwardModel
    {
        $instance = self::create($challenge_placement_id, $award, $printed, $prize);
        $instance->id = $id;
        return $instance;
    }
}