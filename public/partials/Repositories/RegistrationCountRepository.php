<?php

class RegistrationCountRepository implements IRepository{
    private $eventPostID;
    private $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function getAll(): Collection{
        $collection = new Collection();

        global $wpdb;
        $query = <<<SQL
                    WITH
                    ClassRegistrationCount AS(
                        {$this->getClassRegistrationCountQuery()}
                    ),

                    SectionRegistrationCount AS(
                        {$this->getSectionRegistrationCountQuery()}
                    ),

                    GrandChallengeRegistrationCount AS(
                        {$this->getGrandChallengeRegistrationCountQuery()}
                    ),

                    JuniorRegistrationCount AS(
                        {$this->getJuniorRegistrationCountQuery()}
                    )

                    SELECT 
                        index_number, entry_count 
                    FROM
                        ClassRegistrationCount
                    UNION
                    SELECT 
                        index_number, entry_count
                    FROM
                        SectionRegistrationCount
                    UNION
                    SELECT 
                        index_number, entry_count
                    FROM
                        GrandChallengeRegistrationCount
                    Union
                    SELECT 
                        index_number, entry_count
                    FROM
                        JuniorRegistrationCount
                    SQL;

        $registrationCountQueryResults = $wpdb->get_results($query, OBJECT);
        foreach($registrationCountQueryResults as $row){
            $collection->add($row);
        }

        return $collection;
    }

    public function getUserRegistrationCounts(string $userName): Collection{
        global $wpdb;
        $collection = new Collection();
        $registrationCountQueryResults = $wpdb->get_results($this->getClassRegistrationCountQuery($userName), OBJECT);
        foreach($registrationCountQueryResults as $row){
            $collection->add($row);
        }

        return $collection;
    }

    private function getClassRegistrationCountQuery(string|null $userName = null): string{
        $query = QueryBuilder::create()
                            ->select(["class_index AS index_number", "COALESCE(COUNT(".Table::REGISTRATIONS_ORDER->getAlias().".registration_id),0) AS entry_count"])
                            ->from(Table::CLASS_INDICES)
                            ->join("INNER", Table::CLASSES, [Table::CLASS_INDICES], ["id"], ["class_id"])
                            ->join("LEFT", Table::REGISTRATIONS, [Table::CLASS_INDICES], ["class_index_id"], ["id"])
                            ->join("LEFT", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                            ->where(Table::CLASSES->getAlias(), "location_id", "=", $this->locationID)
                            ->where(Table::CLASSES->getAlias(), "class_name", "!=", "Junior")
                            ->whereNested(function(QueryWhereNested $whereNested) use ($userName){
                                $whereNested->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID);
                                if(!empty($userName)){
                                    $whereNested->where(Table::REGISTRATIONS->getAlias(), "user_name", "=", $userName);
                                }
                                $whereNested->whereNull(Table::REGISTRATIONS->getAlias(), "event_post_id", "OR");
                            })
                            ->groupBy(Table::CLASS_INDICES->getAlias(), "id");

        return $query->build();
    }

    private function getSectionRegistrationCountQuery(): string{
        return QueryBuilder::create()
                            ->select(["challenge_index AS index_number", "COALESCE(COUNT(".Table::REGISTRATIONS_ORDER->getAlias().".registration_id),0) AS entry_count"])
                            ->from(Table::CHALLENGE_INDICES)
                            ->join("INNER", Table::CLASSES, [Table::CHALLENGE_INDICES, Table::CHALLENGE_INDICES], ["section", "location_id"], ["section", "location_id"])
                            ->join("INNER", Table::CLASS_INDICES, [Table::CLASSES], ["class_id"], ["id"])
                            ->join("LEFT", Table::REGISTRATIONS, [Table::CLASS_INDICES], ["class_index_id"], ["id"])
                            ->join("LEFT", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                            ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                            ->whereNull(Table::REGISTRATIONS->getAlias(), "event_post_id", "OR")
                            ->where(Table::CHALLENGE_INDICES->getAlias(), "location_id", "=", $this->locationID)
                            ->groupBy(Table::CHALLENGE_INDICES->getAlias(), "id")
                            ->build();
    }

    private function getGrandChallengeRegistrationCountQuery(): string{
        $subQuery = QueryBuilder::create()
                                    ->select([Table::CLASS_INDICES->getAlias().".age", "COUNT(*) AS entry_count"])
                                    ->from(Table::REGISTRATIONS)
                                    ->join("INNER", Table::CLASS_INDICES, [Table::REGISTRATIONS], ["id"], ["class_index_id"])
                                    ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                                    ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=",$this->eventPostID)
                                    ->groupBy(Table::CLASS_INDICES->getAlias(), "age")
                                    ->build();

        return QueryBuilder::create()
                                ->select(["challenge_index AS index_number", "COALESCE(GC_COUNT.entry_count, 0) as entry_count"])
                                ->from(Table::CHALLENGE_INDICES)
                                ->joinSub("LEFT", $subQuery, "GC_COUNT",[Table::CHALLENGE_INDICES->getAlias()], ["age"], ["age"])
                                ->where(Table::CHALLENGE_INDICES->getAlias(), "challenge_name", "=", EventProperties::GRANDCHALLENGE)
                                ->where(Table::CHALLENGE_INDICES->getAlias(), "location_id", "=", $this->locationID)
                                ->build();
    }

    private function getJuniorRegistrationCountQuery(): string{
        $subQuery = QueryBuilder::create()
                                    ->select(["COUNT(*)"])
                                    ->from(Table::REGISTRATIONS)
                                    ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                                    ->join("INNER", Table::REGISTRATIONS_JUNIOR, [Table::REGISTRATIONS_ORDER], ["id"], ["id"])
                                    ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                    ->build();

        return QueryBuilder::create()
                                ->select(["class_index AS index_number", "(".$subQuery.") AS entry_count"])
                                ->from(Table::CLASS_INDICES)
                                ->join("INNER", Table::CLASSES, [Table::CLASS_INDICES], ["id"], ["class_id"])
                                ->where(Table::CLASSES->getAlias(), "class_name", "=", "Junior")
                                ->where(Table::CLASSES->getAlias(), "location_id", "=", $this->locationID)
                                ->build();
    }

    private function getStandardCountQuery(int $countMultiplier, bool $includeStandard, bool $includeOptional){
        $query = QueryBuilder::create()
                                ->select([$countMultiplier."*COUNT(*) AS entry_count"])
                                ->from(Table::REGISTRATIONS)
                                ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                                ->join("INNER", Table::CLASS_INDICES, [Table::REGISTRATIONS], ["id"], ["class_index_id"])
                                ->join("INNER", Table::CLASSES, [Table::CLASS_INDICES], ["id"], ["class_id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->whereNested(function($query) use ($includeStandard, $includeOptional){
                                    if($includeStandard){
                                        $query->where(Table::CLASSES->getAlias(), "section", "!=", "optional");
                                    }
                                    if($includeOptional){
                                        $query->where(Table::CLASSES->getAlias(), "section", "=", "optional", "OR");
                                    }
                                });
                                    
        return $query->build();
    }

    private function getJuniorCountQuery(){
        return QueryBuilder::create()
                                ->select(["COUNT(*) AS entry_count"])
                                ->from(Table::REGISTRATIONS_JUNIOR)
                                ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS_JUNIOR], ["id"], ["id"])
                                ->join("INNER", Table::REGISTRATIONS, [Table::REGISTRATIONS_ORDER], ["id"], ["registration_id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();
    }

    public function getEntryCount(): int{
        global $wpdb;
        $query = <<<SQL
                    WITH
                    StandardCount AS (
                        {$this->getStandardCountQuery(3, true, false)}
                    ),

                    OptionalCount AS (
                        {$this->getStandardCountQuery(1, false, true)}
                    ),

                    JuniorCount AS (
                        {$this->getJuniorCountQuery()}
                    )

                    SELECT 
                        SUM(entry_count) 
                    FROM 
                        (SELECT * FROM StandardCount 
                            UNION 
                        SELECT * FROM OptionalCount 
                            UNION 
                        SELECT * FROM JuniorCount) Counts
                    SQL;

        $entryCount = $wpdb->get_var($query);
        if(!isset($entryCount)){
            return 0;
        }

        return $entryCount;
    }

    public function getExhibitCount(): int{
        global $wpdb;
        $query = <<<SQL
                    WITH
                    ClassCount AS (
                        {$this->getStandardCountQuery(1, true, true)}
                    ),

                    JuniorCount AS (
                        {$this->getJuniorCountQuery()}
                    )

                    SELECT 
                        SUM(entry_count) 
                    FROM 
                        (SELECT * FROM ClassCount 
                            UNION 
                        SELECT * FROM JuniorCount) Counts
                    SQL;
        
        $exhibitCount = $wpdb->get_var($query);
        if(!isset($exhibitCount)){
            return 0;
        }

        return $exhibitCount;
    }
}