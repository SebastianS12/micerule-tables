<?php

class PrizeCardsViewModel{
    public array $printedCards;
    public array $unprintedCards;

    public function __construct()
    {
        $this->printedCards = array();
        $this->unprintedCards = array();
    }

    public function addPrizeCard(PrizeCardModel $prizeCard): void
    {
        if($prizeCard->printed){
            $this->addPrintedCard($prizeCard);
        }else{
            $this->addUnprintedCard($prizeCard);
        }
    }

    private function addPrintedCard(PrizeCardModel $prizeCard): void
    {
        if(!isset($this->printedCards[$prizeCard->userName])){
            $this->printedCards[$prizeCard->userName] = array();
        }
        if(!isset($this->printedCards[$prizeCard->userName][$prizeCard->indexNumber])){
            $this->printedCards[$prizeCard->userName][$prizeCard->indexNumber] = array();
        }

        $this->printedCards[$prizeCard->userName][$prizeCard->indexNumber][$prizeCard->prize->value] = $prizeCard;
    }

    private function addUnprintedCard(PrizeCardModel $prizeCard): void
    {
        if(!isset($this->unprintedCards[$prizeCard->userName])){
            $this->unprintedCards[$prizeCard->userName] = array();
        }
        if(!isset($this->unprintedCards[$prizeCard->userName][$prizeCard->indexNumber])){
            $this->unprintedCards[$prizeCard->userName][$prizeCard->indexNumber] = array();
        }

        $this->unprintedCards[$prizeCard->userName][$prizeCard->indexNumber][$prizeCard->prize->value] = $prizeCard;
    } 

    public function getPrintedCards(): array
    {
        return $this->getPrizeCardsArray($this->printedCards);
    }

    public function getUnprintedCards(): array
    {
        return $this->getPrizeCardsArray($this->unprintedCards);
    }

    private function getPrizeCardsArray(array $nestedPrizeCards): array
    {
        $prizeCards = array();
        foreach($nestedPrizeCards as $fancierCards){
            foreach($fancierCards as $fancierClassCards){
                foreach($fancierClassCards as $fancierClassPrizeCards){
                    $prizeCards[] = $fancierClassPrizeCards;
                }
            }
        }

        return $prizeCards;
    }
}