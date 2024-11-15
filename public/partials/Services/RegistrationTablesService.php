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
        $viewModel->beforeDeadline = time() < EventDeadlineService::getEventDeadline($eventPostID);
        $viewModel->isLoggedIn = is_user_logged_in();
        $viewModel->isMember = EventUser::isMember($userName);
        $viewModel->isAdmin = current_user_can('administrator');

        $challengeIndexModelCollection = $this->challengeIndexRepository->getAll();

        foreach($challengeIndexModelCollection as $challengeIndexModel){
            $viewModel->challengeData[$challengeIndexModel->challenge_name][$challengeIndexModel->age] = $challengeIndexModel;
        }

        $showClassesModels = $this->showClassesRepository->getAll()->with([ClassIndexModel::class], ["id"], ['class_id'], [$this->classIndexRepository]);

        $registrationCountCollection = ($viewModel->beforeDeadline) ? $this->registrationCountRepository->getUserRegistrationCounts($userName) : $this->registrationCountRepository->getAll();

        ModelHydrator::mapAttribute($showClassesModels->{ClassIndexModel::class}, $registrationCountCollection, "registrationCount", "class_index", "index_number","entry_count", 0);
        ModelHydrator::mapAttribute($challengeIndexModelCollection, $registrationCountCollection, "registrationCount", "challenge_index", "index_number", "entry_count", 0);
        
        $classData = array();
        foreach($showClassesModels as $classModel){
            $section = $classModel->section;
            if(!isset($classData[$section])){
                $classData[$section] = array();
            }

            $classData[$section][$classModel->class_name] = array();
            foreach($classModel->classIndices as $classIndexModel){
                $classData[$section][$classModel->class_name][$classIndexModel->age] = array();
                $classData[$section][$classModel->class_name][$classIndexModel->age]["index_number"] = $classIndexModel->class_index;
                $classData[$section][$classModel->class_name][$classIndexModel->age]["entry_count"] = $classIndexModel->registrationCount;
            }
        }
        $viewModel->classData = $classData;

        return $viewModel;
    }

    //TODO: ShowOptionsService
    private function getAllowOnlineRegistrations(int $locationID){
        $showOptionsService = new ShowOptionsService();
        $showOptions = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $locationID);
        $allowOnlineRegistrations = $showOptions->allow_online_registrations;
        return $allowOnlineRegistrations;
    }
}