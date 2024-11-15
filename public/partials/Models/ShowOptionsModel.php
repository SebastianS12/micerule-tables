<?php

class ShowOptionsModel extends Model{
    public int $location_id;
    public bool $allow_online_registrations;
    public float $registration_fee;
    public float $pm_first_place;
    public float $pm_second_place;
    public float $pm_third_place;
    public bool $allow_unstandardised;
    public bool $allow_junior;
    public bool $allow_auction;
    public float $pm_bisec;
    public float $pm_bosec;
    public float $pm_bis;
    public float $pm_boa;

    private function __construct(int $location_id, bool $allow_online_registrations, float $registration_fee, float $pm_first_place, float $pm_second_place, float $pm_third_place, bool $allow_unstandardised, bool $allow_junior, bool $allow_auction, float $pm_bisec, float $pm_bosec, float $pm_bis, float $pm_boa)
    {
        $this->location_id = $location_id;
        $this->allow_online_registrations = $allow_online_registrations;
        $this->registration_fee = $registration_fee;
        $this->pm_first_place = $pm_first_place;
        $this->pm_second_place = $pm_second_place;
        $this->pm_third_place = $pm_third_place;
        $this->allow_unstandardised = $allow_unstandardised;
        $this->allow_junior = $allow_junior;
        $this->allow_auction = $allow_auction;
        $this->pm_bisec = $pm_bisec;
        $this->pm_bosec = $pm_bosec;
        $this->pm_bis = $pm_bis;
        $this->pm_boa = $pm_boa;
    }

    public static function create(int $location_id, bool $allow_online_registrations, float $registration_fee, float $pm_first_place, float $pm_second_place, float $pm_third_place, bool $allow_unstandardised, bool $allow_junior, bool $allow_auction, float $pm_bisec, float $pm_bosec, float $pm_bis, float $pm_boa): ShowOptionsModel
    {
        return new self($location_id, $allow_online_registrations, $registration_fee, $pm_first_place, $pm_second_place, $pm_third_place, $allow_unstandardised, $allow_junior, $allow_auction, $pm_bisec, $pm_bosec, $pm_bis, $pm_boa);
    }

    public static function createWithID(int $id, int $location_id, bool $allow_online_registrations, float $registration_fee, float $pm_first_place, float $pm_second_place, float $pm_third_place, bool $allow_unstandardised, bool $allow_junior, bool $allow_auction, float $pm_bisec, float $pm_bosec, float $pm_bis, float $pm_boa): ShowOptionsModel
    {
        $instance = self::create($location_id, $allow_online_registrations, $registration_fee, $pm_first_place, $pm_second_place, $pm_third_place, $allow_unstandardised, $allow_junior, $allow_auction, $pm_bisec, $pm_bosec, $pm_bis, $pm_boa);
        $instance->id = $id;
        return $instance;
    }

    public static function getDefault(int $location_id): ShowOptionsModel
    {
        return new self($location_id, false, 0.0, 0.0, 0.0, 0.0, false, false, false, 0.0, 0.0, 0.0, 0.0);
    }

    public function getPrizes(): array
    {
        $prizeArray = array();
        $prizeArray["class_first"] = $this->pm_first_place;
        $prizeArray["class_second"] = $this->pm_second_place;
        $prizeArray["class_third"] = $this->pm_third_place;
        $prizeArray["bisec"] = $this->pm_bisec;
        $prizeArray["bosec"] = $this->pm_bosec;
        $prizeArray["bis"] = $this->pm_bis;
        $prizeArray["boa"] = $this->pm_boa;

        return $prizeArray;
    }
}