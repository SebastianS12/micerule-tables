<?php

class RegistrationTablesService{
    private ChallengeIndexRepository $challengeIndexRepository;
    private ShowClassesRepository $showClassesRepository;
    private ClassIndexRepository $classIndexRepository;
    private RegistrationCountRepository $registrationCountRepository;

    public function __construct(ChallengeIndexRepository $challengeIndexRepository, ShowClassesRepository $showClassesRepository, ClassIndexRepository $classIndexRepository, RegistrationCountRepository $registrationCountRepository)
    {
        $this->challengeIndexRepository = $challengeIndexRepository;
        $this->showClassesRepository = $showClassesRepository;
        $this->classIndexRepository = $classIndexRepository;
        $this->registrationCountRepository = $registrationCountRepository;
    }

    public function prepareViewModel(int $eventPostID, int $locationID, string $userName): RegistrationTablesViewModel{
        $viewModel = new RegistrationTablesViewModel();
        $viewModel->allowOnlineRegistrations = $this->getAllowOnlineRegistrations($locationID);
        $viewModel->beforeDeadline = time() < EventProperties::getEventDeadline($eventPostID);
        $viewModel->isLoggedIn = is_user_logged_in();
        $viewModel->isMember = EventUser::isMember($userName);
        $viewModel->isAdmin = current_user_can('administrator');

        $challengeIndexModelCollection = $this->challengeIndexRepository->getAll();

        foreach($challengeIndexModelCollection as $challengeIndexModel){
            $viewModel->challengeData[$challengeIndexModel->challengeName][$challengeIndexModel->age] = $challengeIndexModel;
        }

        $showClassesModels = $this->showClassesRepository->getAll()->with(['indices'], ["id"], ['classID'], [$this->classIndexRepository]);

        $registrationCountCollection = ($viewModel->beforeDeadline) ? $this->registrationCountRepository->getUserRegistrationCounts($userName) : $this->registrationCountRepository->getAll();

        ModelHydrator::mapAttribute($showClassesModels->indices, $registrationCountCollection, "registrationCount", "index", "index_number","entry_count", 0);
        ModelHydrator::mapAttribute($challengeIndexModelCollection, $registrationCountCollection, "registrationCount", "challengeIndex", "index_number", "entry_count", 0);
        
        $classData = array();
        foreach($showClassesModels as $classModel){
            $section = $classModel->sectionName;
            if(!isset($classData[$section])){
                $classData[$section] = array();
            }

            $classData[$section][$classModel->className] = array();
            foreach($classModel->indices as $classIndexModel){
                $classData[$section][$classModel->className][$classIndexModel->age] = array();
                $classData[$section][$classModel->className][$classIndexModel->age]["index_number"] = $classIndexModel->index;
                $classData[$section][$classModel->className][$classIndexModel->age]["entry_count"] = $classIndexModel->registrationCount;
            }
        }
        $viewModel->classData = $classData;

        return $viewModel;
    }

    //TODO: ShowOptionsService
    private function getAllowOnlineRegistrations(int $locationID){
        $showOptionsModel = new ShowOptionsModel();
        $showOptions = $showOptionsModel->getShowOptions($locationID);
        $allowOnlineRegistrations = (isset($showOptions)) ? $showOptions['allow_online_registrations'] : false;
        return $allowOnlineRegistrations;
    }
}