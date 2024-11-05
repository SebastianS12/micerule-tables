<?php

class UserRegistrationModel extends Model{
    public int $id;
    public int $event_post_id;
    public string $user_name;
    public int $class_index_id;

    private function __construct(int $event_post_id, string $user_name, int $class_index_id)
    {
        $this->event_post_id = $event_post_id;
        $this->user_name = $user_name;
        $this->class_index_id = $class_index_id;
    }

    public static function create(int $event_post_id, string $user_name, int $class_index_id){
        return new self($event_post_id, $user_name, $class_index_id);
    }

    public static function createWithID(int $id, int $event_post_id, string $user_name, int $class_index_id){
        $instance = self::create($event_post_id, $user_name, $class_index_id);
        $instance->id = $id;
        return $instance;
    }

    public function classIndex(): ?ClassIndexModel
    {
        return $this->belongsToOne(ClassIndexModel::class, Table::CLASS_INDICES, "class_index_id");
    }

    public function registrationOrder(): Collection
    {
        return $this->hasMany(RegistrationOrderModel::class, Table::REGISTRATIONS_ORDER, "registration_id");
    }

    public function juniorRegistrations(): Collection
    {
        return $this->hasMany(JuniorRegistrationModel::class, Table::REGISTRATIONS_JUNIOR, "registration_id");
    }
}