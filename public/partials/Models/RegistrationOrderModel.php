<?php

class RegistrationOrderModel extends Model{
    public int $registration_id;
    public string $created_at;

    private function __construct(int $registration_id, string $created_at)
    {
        $this->registration_id = $registration_id;
        $this->created_at = $created_at;
    }

    public static function create(int $registration_id, string $created_at): RegistrationOrderModel{
        return new self($registration_id, $created_at);
    }

    public static function createWithID(int $id, int $registration_id, string $created_at): RegistrationOrderModel{
        $instance = self::create($registration_id, $created_at);
        $instance->id = $id;
        return $instance;
    }

    public function entry(): ?EntryModel
    {
        return $this->hasOne(EntryModel::class, Table::ENTRIES, "registration_order_id");
    }

    public function registration(): ?UserRegistrationModel{
        return $this->belongsToOne(UserRegistrationModel::class, Table::REGISTRATIONS, "registration_id");
    }

    public function juniorRegistration(): ?JuniorRegistrationModel
    {
        return $this->hasOne(JuniorRegistrationModel::class, Table::REGISTRATIONS_JUNIOR, "registration_order_id");
    }
}