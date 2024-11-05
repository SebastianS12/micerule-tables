<?php

class AwardsRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::AWARDS->getAlias().".*"])
                                ->from(Table::AWARDS)
                                ->join("INNER", Table::CHALLENGE_PLACEMENTS, [Table::AWARDS], ["id"], ["challenge_placement_id"])
                                ->join("INNER", Table::ENTRIES, [Table::CHALLENGE_PLACEMENTS], ["id"], ["entry_id"])
                                ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::ENTRIES], ["id"], ["registration_order_id"])
                                ->join("INNER", Table::REGISTRATIONS, [Table::REGISTRATIONS_ORDER], ["id"], ["registration_id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();

        global $wpdb;
        $awardsQueryResults = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();
        foreach($awardsQueryResults as $row){
            $awardModel = AwardModel::createWithID($row['id'], $row['challenge_placement_id'], $row['award'], $row['printed'], $row['prize']);
            $collection->add($awardModel);
        }

        return $collection;
    }

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