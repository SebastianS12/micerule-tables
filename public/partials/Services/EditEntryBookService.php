<?php

class EditEntryBookService{

    public function getSelectOptions(ShowClassesRepository $showClassesRepository, ClassIndexRepository $classIndexRepository): array
    {
        $showClassesCollection = $showClassesRepository->getAll()->with([ClassIndexModel::class], ["id"], ["class_id"], [$classIndexRepository]);
        $selectOptions = array();
        foreach($showClassesCollection as $entryClassModel){
            foreach($entryClassModel->classIndices as $classIndexModel){
                if(!isset($selectOptions[$entryClassModel->section])){
                    $selectOptions[$entryClassModel->section] = array();
                }
    
                $selectOption = array();
                $selectOption['className'] = $entryClassModel->class_name;
                $selectOption['age'] = $classIndexModel->age;
                $selectOption['classIndex'] = $classIndexModel->class_index;
                $selectOption['index_id'] = $classIndexModel->id;
                $selectOptions[$entryClassModel->section][] = $selectOption;
            }
        }

        return $selectOptions;
    }
}