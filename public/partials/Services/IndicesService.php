<?php

class IndicesService{
    public function updateIndices(int $locationID): void
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $classIndexRepository = new ClassIndexRepository($locationID);
        $showClassesCollection = $showClassesRepository->getAll()->with([ClassIndexModel::class], ["id"], ["class_id"], [$classIndexRepository]);

        $challengeIndexRepository = new ChallengeIndexRepository($locationID);
        $challengeIndexCollection = $challengeIndexRepository->getAll()->groupBy("section");

        $index = 1;
        foreach($showClassesCollection->whereNot("section", "optional")->groupBy("section") as $sectionName => $sectionClassCollection){
            foreach($sectionClassCollection as $entryClassModel){
                $classIndexModels = $entryClassModel->classIndices()->groupByUniqueKey("age");
                $adClassIndexModel = (isset($classIndexModels['Ad'])) ? $classIndexModels['Ad'] : ClassIndexModel::create($index, $entryClassModel->id, "Ad");
                $adClassIndexModel->class_index = $index;
                $classIndexRepository->save($adClassIndexModel);

                $u8ClassIndexModel = (isset($classIndexModels['U8'])) ? $classIndexModels['U8'] : ClassIndexModel::create($index + 1, $entryClassModel->id, "U8");
                $u8ClassIndexModel->class_index = $index + 1;
                $classIndexRepository->save($u8ClassIndexModel);

                $index += 2;
            }

            $this->updateChallengeIndices($sectionName, $challengeIndexCollection[$sectionName], $index, $challengeIndexRepository, $locationID);
            $index += 2;
        }

        $this->updateChallengeIndices(Section::GRAND_CHALLENGE->value, $challengeIndexCollection[Section::GRAND_CHALLENGE->value], $index, $challengeIndexRepository, $locationID);
        $index += 2;

        foreach($showClassesCollection->where("section", "optional") as $entryClassModel){
            $classIndexModels = $entryClassModel->classIndices()->groupByUniqueKey("age");
            $classIndexModel = (isset($classIndexModels['AA'])) ? $classIndexModels['AA'] : ClassIndexModel::create($index, $entryClassModel->id, "AA");
            $classIndexModel->class_index = $index;
            $classIndexRepository->save($classIndexModel);
            $index++;
        }
    }

    private function updateChallengeIndices(string $sectionName, ?Collection $sectionChallengeIndexCollection, int $index, ChallengeIndexRepository $challengeIndexRepository, int $locationID): void
    {
        $section = Section::from($sectionName);
        if($sectionChallengeIndexCollection === null){
            $adChallengeIndexModel = ChallengeIndexModel::create($locationID, $sectionName, $section->getChallengeName(), "Ad", $index);
            $challengeIndexRepository->save($adChallengeIndexModel);
            $u8ChallengeIndexModel = ChallengeIndexModel::create($locationID, $sectionName, $section->getChallengeName(), "U8", $index + 1);
            $challengeIndexRepository->save($u8ChallengeIndexModel);
        }else{
            $sectionChallengeCollection = $sectionChallengeIndexCollection->groupByUniqueKey("age");
            $adChallengeIndexModel = (isset($sectionChallengeCollection['Ad'])) ? $sectionChallengeCollection['Ad'] : ChallengeIndexModel::create($locationID, $sectionName, $section->getChallengeName(), "Ad", $index);
            $adChallengeIndexModel->challenge_index = $index;
            $challengeIndexRepository->save($adChallengeIndexModel);

            $u8ChallengeIndexModel = (isset($sectionChallengeCollection['U8'])) ? $sectionChallengeCollection['U8'] : ChallengeIndexModel::create($locationID, $sectionName, $section->getChallengeName(), "U8", $index + 1);
            $u8ChallengeIndexModel->challenge_index = $index + 1;
            $challengeIndexRepository->save($u8ChallengeIndexModel);
        }
    }

    public function swapClassIndices(int $firstClassID, int $secondClassID, ShowClassesRepository $showClassesRepository, ClassIndexRepository $classIndexRepository): void
    {
        $firstClassModel = $showClassesRepository->getByID($firstClassID);
        $secondClassModel = $showClassesRepository->getByID($secondClassID);
        if($firstClassModel !== null && $secondClassModel !== null){
            $firstClassIndices = $firstClassModel->classIndices()->groupByUniqueKey("age");
            $secondClassIndices = $secondClassModel->classIndices()->groupByUniqueKey("age");

            foreach($firstClassIndices as $age => $firstClassIndexModel){
                if(isset($secondClassIndices[$age])){
                    $secondClassIndexModel = $secondClassIndices[$age];
                    $tmp = $firstClassIndexModel->class_index;
                    $firstClassIndexModel->class_index = $secondClassIndexModel->class_index;
                    $secondClassIndexModel->class_index = $tmp;

                    $classIndexRepository->save($firstClassIndexModel);
                    $classIndexRepository->save($secondClassIndexModel);
                }
            }
        }
    }
}