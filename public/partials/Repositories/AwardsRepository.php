<?php

class AwardsRepository{
    public function getByPlacementID($placementID){
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_show_challenge_awards
                               WHERE challenge_placement_id = ".$placementID, ARRAY_A);
    }
    public function addAward(int $prizeID, int $placementID, string $award){
        global $wpdb;
        $wpdb->insert($wpdb->prefix."micerule_show_challenge_awards", array('challenge_placement_id' => $placementID, 'award' => $award, 'printed' => false, 'prize' => $prizeID));
    }

    public function removeAward(int $awardID){
        global $wpdb;
        $wpdb->delete($wpdb->prefix."micerule_show_challenge_awards", array('id' => $awardID));
    }
}