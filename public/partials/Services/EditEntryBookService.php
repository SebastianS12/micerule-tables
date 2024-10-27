<?php

class EditEntryBookService{

    public function getSelectOptions(ShowClassesRepository $showClassesRepository, ClassIndexRepository $classIndexRepository): array
    {
        $showClassesCollection = $showClassesRepository->getAll()->with(["indices"], ["id"], ["classID"], [$classIndexRepository]);
        $selectOptions = array();
        foreach($showClassesCollection as $entryClassModel){
            foreach($entryClassModel->indices as $classIndexModel){
                if(!isset($selectOptions[$entryClassModel->sectionName])){
                    $selectOptions[$entryClassModel->sectionName] = array();
                }
    
                $selectOption = array();
                $selectOption['className'] = $entryClassModel->class_name;
                $selectOption['age'] = $classIndexModel->age;
                $selectOption['classIndex'] = $classIndexModel->index;
                $selectOption['index_id'] = $classIndexModel->id;
                $selectOptions[$entryClassModel->section][] = $selectOption;
            }
        }

        return $selectOptions;
    }
}