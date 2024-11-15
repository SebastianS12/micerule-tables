<?php

interface IPlacementModel{
    public static function create(int $entry_id, int $index_id, int $placement, int $prize, bool $printed): PlacementModel;
    public static function createWithID(int $id, int $entry_id, int $index_id, int $placement, int $prize, bool $printed): PlacementModel;
    public function entry(): ?EntryModel;
    public function registration(): ?UserRegistrationModel;
}

// class PlacementModel extends Model{
//     public int $entry_id;
//     public int $index_id;
//     public int $placement;
//     public Prize $prize;
//     public bool $printed;

//     protected function __construct(int $entry_id, int $index_id, int $placement, int $prize, bool $printed){
//         $this->entry_id = $entry_id;
//         $this->index_id = $index_id;
//         $this->placement = $placement;
//         $this->prize = Prize::from($prize);
//         $this->printed = $printed;
//     }

//     public static function create(int $entry_id, int $index_id, int $placement, int $prize, bool $printed): PlacementModel{
//         $instance = new self($entry_id, $index_id, $placement, $prize, $printed);
//         return $instance;
//     }

//     public static function createWithID(int $id, int $entry_id, int $index_id, int $placement, int $prize, bool $printed): PlacementModel{
//         $instance = new self($entry_id, $index_id, $placement, $prize, $printed);
//         $instance->id = $id;
//         return $instance;
//     }

//     public function entry(): ?EntryModel
//     {
//         return $this->belongsToOne(EntryModel::class, Table::ENTRIES, "entry_id");
//     }

//     public function registration(): ?UserRegistrationModel
//     {
//         $relations = [EntryModel::class, RegistrationOrderModel::class, UserRegistrationModel::class];
//         $relationTables = [Table::ENTRIES, Table::REGISTRATIONS_ORDER, Table::REGISTRATIONS];
//         $foreignKeys = ["entry_id", "registration_order_id", "registration_id"];
//         return $this->belongsToOneThrough($relations, $relationTables, $foreignKeys);
//     }
// }

class PlacementModel extends Model{
    public int $entry_id;
    public int $index_id;
    public int $placement;
    public Prize $prize;
    public bool $printed;

    protected function __construct(int $entry_id, int $index_id, int $placement, int $prize, bool $printed){
        $this->entry_id = $entry_id;
        $this->index_id = $index_id;
        $this->placement = $placement;
        $this->prize = Prize::from($prize);
        $this->printed = $printed;
    }

    public function entry(): ?EntryModel
    {
        return $this->belongsToOne(EntryModel::class, Table::ENTRIES, "entry_id");
    }

    public function registration(): ?UserRegistrationModel
    {
        $relations = [EntryModel::class, RegistrationOrderModel::class, UserRegistrationModel::class];
        $relationTables = [Table::ENTRIES, Table::REGISTRATIONS_ORDER, Table::REGISTRATIONS];
        $foreignKeys = ["entry_id", "registration_order_id", "registration_id"];
        return $this->belongsToOneThrough($relations, $relationTables, $foreignKeys);
    }
}

class ClassPlacementModel extends PlacementModel implements IPlacementModel{
    public static function create(int $entry_id, int $index_id, int $placement, int $prize, bool $printed): ClassPlacementModel{
        $instance = new self($entry_id, $index_id, $placement, $prize, $printed);
        return $instance;
    }

    public static function createWithID(int $id, int $entry_id, int $index_id, int $placement, int $prize, bool $printed): ClassPlacementModel{
        $instance = new self($entry_id, $index_id, $placement, $prize, $printed);
        $instance->id = $id;
        return $instance;
    }

    public function report(): ?PlacementReport
    {
        return $this->hasOne(PlacementReport::class, Table::CLASS_REPORTS, "placement_id");
    }
}

class ChallengePlacementModel extends PlacementModel implements IPlacementModel{
    public static function create(int $entry_id, int $index_id, int $placement, int $prize, bool $printed): ChallengePlacementModel{
        $instance = new self($entry_id, $index_id, $placement, $prize, $printed);
        return $instance;
    }

    public static function createWithID(int $id, int $entry_id, int $index_id, int $placement, int $prize, bool $printed): ChallengePlacementModel{
        $instance = new self($entry_id, $index_id, $placement, $prize, $printed);
        $instance->id = $id;
        return $instance;
    }

    public function award(): ?AwardModel
    {
        return $this->hasOne(AwardModel::class, Table::AWARDS, "challenge_placement_id");
    }
}
