<?php

class ShowClassesService{
    public function prepareViewModel(int $locationID): ShowClassesViewModel
    {
        $canEditShowClasses = PermissionHelper::canEditShowClasses($locationID);
        $viewModel = new ShowClassesViewModel($canEditShowClasses);

        $showClassesRepository = new ShowClassesRepository($locationID);
        $classIndexRepository = new ClassIndexRepository($locationID);
        $showClassesCollection = $showClassesRepository->getAll()->with([ClassIndexModel::class], ["id"], ["class_id"], [$classIndexRepository]);

        foreach($showClassesCollection->whereNot("section", "optional") as $entryClassModel){
            $viewModel->addClass($entryClassModel);
        }

        foreach($showClassesCollection->where("section", "optional") as $entryClassModel){
            $viewModel->addOptionalClass($entryClassModel);
        }

        $challengeIndexRepository = new ChallengeIndexRepository($locationID);
        $challengeIndexCollection = $challengeIndexRepository->getAll()->groupBy("section");
        foreach($challengeIndexCollection as  $sectionChallengeIndexCollection){
            $adChallengeIndexModel = $sectionChallengeIndexCollection->get("age", "Ad");
            $u8ChallengeIndexModel = $sectionChallengeIndexCollection->get("age", "U8");
            if($adChallengeIndexModel !== null && $u8ChallengeIndexModel !== null){
                $viewModel->addChallenge($adChallengeIndexModel, $u8ChallengeIndexModel);
            }
        }

        return $viewModel;
    }

    public function delete(int $classID, ShowClassesRepository $showClassesRepository): void
    {
        $showClassesRepository->delete($classID);
    }

    public function updateSectionPositions(string $sectionName, ShowClassesRepository $showClassesRepository): void
    {
        $sectionClassCollection = $showClassesRepository->getAll(function(QueryBuilder $query) use ($sectionName){
            $query->where(Table::CLASSES->getAlias(), "section", "=", $sectionName);
        });

        foreach($sectionClassCollection as $position => $entryClassModel){
            $entryClassModel->section_position = $position;
            $showClassesRepository->save($entryClassModel);
        }
    }

    public function addClass(int $locationID, string $className, string $section, ShowClassesRepository $showClassesRepository): void
    {
        $sectionClassCollection = $showClassesRepository->getAll(function(QueryBuilder $query) use ($section){
            $query->where(Table::CLASSES->getAlias(), "section", "=", $section);
        });

        $nextSectionPosition = ($sectionClassCollection->last() !== null) ? $sectionClassCollection->last()->section_position + 1 : 0;
        $entryClassModel = EntryClassModel::create($locationID, $className, $section, $nextSectionPosition);
        $showClassesRepository->save($entryClassModel);
    }

    public function swapClasses(int $firstClassID, int $secondClassID, ShowClassesRepository $showClassesRepository): void
    {
        $firstClassModel = $showClassesRepository->getByID($firstClassID);
        $secondClassModel = $showClassesRepository->getByID($secondClassID);

        if($firstClassModel !== null && $secondClassModel !== null){
            $temp = $firstClassModel->section_position;
            $firstClassModel->section_position = $secondClassModel->section_position;
            $secondClassModel->section_position = $temp;

            $showClassesRepository->save($firstClassModel);
            $showClassesRepository->save($secondClassModel);
        }
    }
}