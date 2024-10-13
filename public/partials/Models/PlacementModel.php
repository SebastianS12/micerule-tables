<?php

class PlacementModel extends Model{
    public int $entryID;
    public int $indexID;
    public int $placement;
    public Prize $prize;
    public bool $printed;

    protected function __construct(int $entryID, int $indexID, int $placement, int $prize, bool $printed){
        $this->entryID = $entryID;
        $this->indexID = $indexID;
        $this->placement = $placement;
        $this->prize = Prize::from($prize);
        $this->printed = $printed;
    }

    public static function create(int $entryID, int $indexID, int $placement, int $prize, bool $printed){
        $instance = new self($entryID, $indexID, $placement, $prize, $printed);
        return $instance;
    }

    public static function createWithID(int $id, int $entryID, int $indexID, int $placement, int $prize, bool $printed){
        $instance = new self($entryID, $indexID, $placement, $prize, $printed);
        $instance->id = $id;
        return $instance;
    }

    public function entry(): EntryModel|null
    {
        return $this->belongsToOne(EntryModel::class);
    }
}
