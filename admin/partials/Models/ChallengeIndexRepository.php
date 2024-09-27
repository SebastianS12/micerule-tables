<?php

class ChallengeIndexRepository{
    private $locationID;
    public function __construct($locationID){
        $this->locationID = $locationID;
    }

    public function getChallengeIndexModel($challengeName, $age){
        global $wpdb;
        $challengeIndexData = $wpdb->get_row("SELECT id, section, challenge_name, age, challenge_index 
                                          FROM ".$wpdb->prefix."micerule_show_challenges_indices CI
                                          WHERE location_id = ".$this->locationID." AND challenge_name = '".$challengeName."' AND age = '".$age."'", ARRAY_A);

        return ChallengeIndexModel::createWithID($challengeIndexData['id'], $this->locationID, $challengeIndexData['section'], $challengeIndexData['challenge_name'], $challengeIndexData['age'], $challengeIndexData['challenge_index']);
    }
}