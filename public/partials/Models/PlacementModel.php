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

    public static function create(int $entryID, int $indexID, int $placement, int $prize, bool $printed): PlacementModel{
        $instance = new self($entryID, $indexID, $placement, $prize, $printed);
        return $instance;
    }

    public static function createWithID(int $id, int $entryID, int $indexID, int $placement, int $prize, bool $printed): PlacementModel{
        $instance = new self($entryID, $indexID, $placement, $prize, $printed);
        $instance->id = $id;
        return $instance;
    }

    public function entry(): EntryModel|null
    {
        return $this->belongsToOne(EntryModel::class);
    }

    public function award(): AwardModel|null
    {
        return $this->hasOne("award");
    }

    public function registration(): UserRegistrationModel|null
    {
        return $this->belongsToOneThrough([EntryModel::class, RegistrationOrderModel::class, UserRegistrationModel::class]);
    }
}
