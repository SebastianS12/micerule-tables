<?php

class JuniorRegistrationModel extends Model{
    public int $registration_order_id;
    public int $registration_id;

    private function __construct(int $registration_order_id, int $registration_id)
    {
        $this->registration_order_id = $registration_order_id;
        $this->registration_id = $registration_id;
    }

    public static function create(int $registration_order_id, int $registration_id): JuniorRegistrationModel
    {
        return new self($registration_order_id, $registration_id);
    }

    public static function createWithID(int $id, int $registration_order_id, int $registration_id): JuniorRegistrationModel
    {
        $instance = self::create($registration_order_id, $registration_id);
        $instance->id = $id;
        return $instance;
    }

    public function order(): ?RegistrationOrderModel
    {
        return $this->belongsToOne(RegistrationOrderModel::class, Table::REGISTRATIONS_ORDER, "registration_order_id");
    }
}