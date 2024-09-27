<?php

class PrizeCardsService{
    private PrizeCardsRepository $prizeCardsRepository;

    public function __construct(PrizeCardsRepository $prizeCardsRepository)
    {
        $this->prizeCardsRepository = $prizeCardsRepository;
    }

    public function preparePrizeCardsData(int $eventPostID, JudgesService $judgesService): array{
        $prizeCards = [
            'printed' => [],
            'unprinted' => []
        ];
        
        $judgesNamesString = $judgesService->getJudgesNamesString($eventPostID);
        foreach($this->prizeCardsRepository->getAll($eventPostID, EventProperties::getEventLocationID($eventPostID)) as $prizeCardData) {
            $prizeCard = PrizeCardFactory::getPrizeCardModel($prizeCardData, $judgesNamesString);
            
            $targetArray = $prizeCardData['printed'] ? 'printed' : 'unprinted';
            $prizeCards[$targetArray][] = $prizeCard;
        }
        
        return $prizeCards;
    }

    public function printAll(int $eventPostID){
        foreach($this->prizeCardsRepository->getAll($eventPostID, EventProperties::getEventLocationID($eventPostID)) as $prizeCardData){
            if(!(bool)$prizeCardData['printed']){
                $printDAO = PrintDAOFactory::getPrintDAO($prizeCardData['prize']);
                $this->prizeCardsRepository->updatePrinted((int)$prizeCardData['placement_id'], true, $printDAO);
            }
        }
    }

    public function moveToUnprinted(int $placementID, int $prizeID){
        $printDAO = PrintDAOFactory::getPrintDAO($prizeID);
        $this->prizeCardsRepository->updatePrinted($placementID, false, $printDAO);
    }
}