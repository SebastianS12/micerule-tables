<?php

class EntryBookService{
    private ChallengeIndexRepository $challengeIndexRepository;
    private PlacementsRepository $challengePlacementsRepository;
    private ChallengeRowService $challengeRowService;
    private ShowSectionRepository $showSectionRepository;

    public function __construct(ChallengeIndexRepository $challengeIndexRepository, PlacementsRepository $challengePlacementsRepository, ChallengeRowService $challengeRowService, ShowSectionRepository $showSectionRepository)
    {
        $this->challengeIndexRepository = $challengeIndexRepository;
        $this->challengePlacementsRepository = $challengePlacementsRepository;
        $this->challengeRowService = $challengeRowService;
        $this->showSectionRepository = $showSectionRepository;
    }

    public function prepareViewModel(int $eventPostID): EntryBookViewModel{
        $entryBookViewModel = new EntryBookViewModel();

        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $sectionName = strtolower($sectionName);
            $adSectionIndexModel = $this->challengeIndexRepository->getChallengeIndexModel(EventProperties::getChallengeName($sectionName), "Ad");
            $u8SectionIndexModel = $this->challengeIndexRepository->getChallengeIndexModel(EventProperties::getChallengeName($sectionName), "U8");
            $adSectionPlacements = $this->challengePlacementsRepository->getAllPlacements($eventPostID, $adSectionIndexModel->id);//new SectionPlacements($eventPostID, "Ad", $sectionName);
            $u8SectionPlacements = $this->challengePlacementsRepository->getAllPlacements($eventPostID, $u8SectionIndexModel->id);
            $entryBookViewModel->addSectionData($sectionName, $this->challengeRowService->getChallengeRowData(EventProperties::getChallengeName($sectionName), Prize::SECTION));

            foreach($this->showSectionRepository->getShowSectionClassNames($sectionName) as $className){
                // new service for classes: className, classIndex, classPenNumbers
            }
        }

        return $entryBookViewModel;
    }
}