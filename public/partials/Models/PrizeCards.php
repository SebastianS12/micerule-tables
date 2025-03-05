<?php
abstract class PrizeCardModel{
    public $placementID;
    public $placement;
    public $prize;
    public $age;
    public $userName;
    public $varietyName;
    public $penNumber;
    public $indexNumber;
    public $className;
    public $section;
    public $printed;
    public $judge;
    public $entryCount;
    public $award;
    public $date;
    public $prizeClass;
    public $placementClass;
    public $displayedPlacement;

    const placementClasses = array(1 => "first", 2 => "second", 3 => "third");

    public function __construct(int $placementID, int $placement, Prize $prize, string $age, string $userName, string $className, string $varietyName, int $penNumber, int $indexNumber, string $section, bool $printed, ?string $judge, int $entryCount, ?string $award)
    {
        $this->placementID = $placementID;
        $this->placement = $placement;
        $this->prize = $prize;
        $this->age = $age;
        $this->userName = $userName;
        $this->className = $className;
        $this->varietyName = $varietyName;
        $this->penNumber = $penNumber;
        $this->indexNumber = $indexNumber;
        $this->section = $section;
        $this->printed = $printed;
        $this->judge = $judge;
        $this->entryCount = $entryCount;
        $this->award = $award;
        $this->date = date("d/m/Y");
        $this->placementClass =  self::placementClasses[$placement];
        $this->displayedPlacement = $this->getDisplayedPlacement();
    }

    abstract protected function getDisplayedPlacement(): string;
}

class ClassPrizeCard extends PrizeCardModel{
    public function __construct(int $placementID, int $placement, Prize $prize, string $age, string $userName, string $className, string $varietyName, int $penNumber, int $indexNumber, string $section, bool $printed, ?string $judge, int $entryCount, ?string $award)
    {
        parent::__construct($placementID, $placement, $prize, $age, $userName, $className, $varietyName, $penNumber, $indexNumber, $section, $printed, $judge, $entryCount, $award);
        $this->prizeClass = "breed-class";
    }

    public function getDisplayedPlacement(): string{
        return $this->placement;
    }
}

class JuniorPrizeCard extends PrizeCardModel{
    public function __construct(int $placementID, int $placement, Prize $prize, string $age, string $userName, string $className, string $varietyName, int $penNumber, int $indexNumber, string $section, bool $printed, ?string $judge, int $entryCount, ?string $award)
    {
        parent::__construct($placementID, $placement, $prize, $age, $userName, $className, $varietyName, $penNumber, $indexNumber, $section, $printed, $judge, $entryCount, $award);
        $this->prizeClass = "junior-challenge";
    }

    public function getDisplayedPlacement(): string
    {
        $displayedPlacements = array(1 => "Best Junior", 2 => "2nd Junior", 3 => "3rd Junior");
        return $displayedPlacements[$this->placement];
    }
}

class SectionPrizeCard extends PrizeCardModel{
    public function __construct(int $placementID, int $placement, Prize $prize, string $age, string $userName, string $className, string $varietyName, int $penNumber, int $indexNumber, string $section, bool $printed, ?string $judge, int $entryCount, ?string $award)
    {
        parent::__construct($placementID, $placement, $prize, $age, $userName, $className, $varietyName, $penNumber, $indexNumber, $section, $printed, $judge, $entryCount, $award);
        $this->prizeClass = "section-challenge";
    }

    public function getDisplayedPlacement(): string{
        if(isset($this->award) && Award::from($this->award) == Award::BIS){
            return SectionAward::BIS->value;
        }
        if(isset($this->award) && Award::from($this->award) == Award::BOA){
            return SectionAward::BOA->value;
        }
        return $this->placement;
    }
}

class GrandChallengePrizeCard extends PrizeCardModel{
    public function __construct(int $placementID, int $placement, Prize $prize, string $age, string $userName, string $className, string $varietyName, int $penNumber, int $indexNumber, string $section, bool $printed, ?string $judge, int $entryCount, ?string $award)
    {
        parent::__construct($placementID, $placement, $prize, $age, $userName, $className, $varietyName, $penNumber, $indexNumber, $section, $printed, $judge, $entryCount, $award);
        $this->prizeClass = "grand-challenge";
    }

    public function getDisplayedPlacement(): string{
        if(isset($this->award) && Award::from($this->award) == Award::BIS){
            return GrandChallengeAward::BIS->value;
        }
        if(isset($this->award) && Award::from($this->award) == Award::BOA){
            return GrandChallengeAward::BOA->value;
        }
        return $this->placement;
    }
}
