<?php

class AwardModel extends Model{
    public int $challengePlacementID;
    public Award $award;
    public bool $printed;
    public Prize $prize;

    private function __construct(int $challengePlacementID, string $award, bool $printed, int $prize)
    {
        $this->challengePlacementID = $challengePlacementID;
        $this->award = Award::from($award);
        $this->printed = $printed;
        $this->prize = Prize::from($prize);
    }

    public static function create(int $challengePlacementID, string $award, bool $printed, int $prize): AwardModel
    {
        return new self($challengePlacementID, $award, $printed, $prize);
    }

    public static function createWithID(int $id, int $challengePlacementID, string $award, bool $printed, int $prize): AwardModel
    {
        $instance = self::create($challengePlacementID, $award, $printed, $prize);
        $instance->id = $id;
        return $instance;
    }
}