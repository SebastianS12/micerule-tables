<?php

class ShowOptions implements JsonSerializable{
    public bool $allowOnlineRegistrations;
    public float $registrationFee;
    public array $optionalClasses;
    public array $prizeMoney;

    private function __construct(bool $allowOnlineRegistrations = false, float $registrationFee = 0, bool $unstandardised = false, bool $junior = false, bool $auction = false, float $firstPrize = 0, float $secondPrize = 0, float $thirdPrize = 0){
        $this->allowOnlineRegistrations = $allowOnlineRegistrations;
        $this->registrationFee = $registrationFee;
        $this->optionalClasses = array();
        $this->optionalClasses['unstandardised'] = $unstandardised;
        $this->optionalClasses['junior'] = $junior;
        $this->optionalClasses['auction'] = $auction;
        $this->prizeMoney = array();
        $this->prizeMoney['firstPrize'] = $firstPrize;
        $this->prizeMoney['secondPrize'] = $secondPrize;
        $this->prizeMoney['thirdPrize'] = $thirdPrize;
    }

    public static function createFromPostMeta(ShowOptions|string $postMetaShowOptions): ShowOptions
    {
        if($postMetaShowOptions == ""){
            return new self;
        }

        return $postMetaShowOptions;
    }

    public static function create(bool $allowOnlineRegistrations = false, float $registrationFee = 0, bool $unstandardised = false, bool $junior = false, bool $auction = false, float $firstPrize = 0, float $secondPrize = 0, float $thirdPrize = 0): ShowOptions
    {
        return new self($allowOnlineRegistrations, $registrationFee, $unstandardised, $junior, $auction, $firstPrize, $secondPrize, $thirdPrize);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    // public static function convert(){
    //     global $wpdb;
    //     $options = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_show_options", ARRAY_A);
    //     $showOptionsRepository = new ShowOptionsRepository();
    //     foreach($options as $row){
    //         $showOptions = ShowOptions::create($row['allow_online_registrations'], $row['registration_fee'], $row['allow_unstandardised'], $row['allow_junior'], $row['allow_auction'], $row['pm_first_place'], $row['pm_second_place'], $row['pm_third_place']);
    //         $showOptionsRepository->saveShowOptions($row['location_id'], $showOptions);
    //     }
    // }

    public static function convert(){
        $showOptionsRepository = new ShowOptionsRepository();
        global $wpdb;
        $locationIDs = $wpdb->get_col("SELECT post_id FROM sm1_postmeta WHERE meta_key = 'micerule_show_options'");
        foreach($locationIDs as $locationID){
            $showOptions = get_post_meta($locationID, "micerule_show_options");
            $showOptionsModel = ShowOptionsModel::create($locationID, $showOptions->allowOnlineRegistrations, $showOptions->registrationFee, $showOptions->prizeMoney['firstPlace'], $showOptions->prizeMoney['secondPlace'], $showOptions->prizeMoney['thirdPlace'], $showOptions->optionalClasses['unstandardised'], $showOptions->optionalClasses['junior'], $showOptions->optionalClasses['auction'], 0.0, 0.0, 0.0, 0.0);
            $showOptionsRepository->saveShowOptions($showOptionsModel);
        }
    }
}