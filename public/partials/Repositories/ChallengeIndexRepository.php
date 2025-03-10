<?php

class ChallengeIndexRepository implements IRepository{
    private $locationID;
    public function __construct($locationID){
        $this->locationID = $locationID;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection
    {
        global $wpdb;
        $challengeIndexQueryData = $wpdb->get_results("SELECT id, section, challenge_name, age, challenge_index 
                                          FROM ".$wpdb->prefix."micerule_show_challenges_indices CI
                                          WHERE location_id = ".$this->locationID, ARRAY_A);

        $collection = new Collection();
        foreach($challengeIndexQueryData as $challengeIndexData){
            $challengeIndexModel = ChallengeIndexModel::createWithID($challengeIndexData['id'], $this->locationID, $challengeIndexData['section'], $challengeIndexData['challenge_name'], $challengeIndexData['age'], $challengeIndexData['challenge_index']);
            $collection->add($challengeIndexModel);
        }

        return $collection;
    }

    public function getChallengeIndexModel($challengeName, $age){
        global $wpdb;
        $challengeIndexData = $wpdb->get_row("SELECT id, section, challenge_name, age, challenge_index 
                                          FROM ".$wpdb->prefix."micerule_show_challenges_indices CI
                                          WHERE location_id = ".$this->locationID." AND challenge_name = '".$challengeName."' AND age = '".$age."'", ARRAY_A);

        return ChallengeIndexModel::createWithID($challengeIndexData['id'], $this->locationID, $challengeIndexData['section'], $challengeIndexData['challenge_name'], $challengeIndexData['age'], $challengeIndexData['challenge_index']);
    }

    public function save(ChallengeIndexModel $challengeIndexModel): void
    {
        global $wpdb;
        if(isset($challengeIndexModel->id)){
            $wpdb->update($wpdb->prefix.Table::CHALLENGE_INDICES->value, array('location_id' => $challengeIndexModel->location_id, 'section' => $challengeIndexModel->section, 'challenge_name' => $challengeIndexModel->challenge_name, 'age' => $challengeIndexModel->age, 'challenge_index' => $challengeIndexModel->challenge_index), array('id' => $challengeIndexModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::CHALLENGE_INDICES->value, array('location_id' => $challengeIndexModel->location_id, 'section' => $challengeIndexModel->section, 'challenge_name' => $challengeIndexModel->challenge_name, 'age' => $challengeIndexModel->age, 'challenge_index' => $challengeIndexModel->challenge_index));
        }
    }
}