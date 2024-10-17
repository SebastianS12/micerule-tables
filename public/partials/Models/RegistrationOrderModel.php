<?php

class RegistrationOrderModel extends Model{
    public int $registrationID;
    public string $createdAt;

    private function __construct(int $registrationID, string $createdAt)
    {
        $this->registrationID = $registrationID;
        $this->createdAt = $createdAt;
    }

    public static function create(int $registrationID, string $createdAt): RegistrationOrderModel{
        return new self($registrationID, $createdAt);
    }

    public static function createWithID(int $id, int $registrationID, string $createdAt): RegistrationOrderModel{
        $instance = self::create($registrationID, $createdAt);
        $instance->id = $id;
        return $instance;
    }

    public function entry(): EntryModel|null{
        return $this->hasOne("entry");
    }

    public function registration(): UserRegistrationModel|null{
        return $this->belongsToOne(UserRegistrationModel::class);
    }
}