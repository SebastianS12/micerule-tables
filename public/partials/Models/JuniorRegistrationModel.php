<?php

class JuniorRegistrationModel extends Model{
    public int $registrationOrderID;
    public int $registrationID;

    private function __construct(int $registrationOrderID, int $registrationID)
    {
        $this->registrationOrderID = $registrationOrderID;
        $this->registrationID = $registrationID;
    }

    public static function create(int $registrationOrderID, int $registrationID): JuniorRegistrationModel
    {
        return new self($registrationOrderID, $registrationID);
    }

    public static function createWithID(int $id, int $registrationOrderID, int $registrationID): JuniorRegistrationModel
    {
        $instance = self::create($registrationOrderID, $registrationID);
        $instance->id = $id;
        return $instance;
    }

    public function order(): RegistrationOrderModel|null
    {
        return $this->belongsToOne(RegistrationOrderModel::class);
    }
}