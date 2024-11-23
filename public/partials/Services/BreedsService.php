<?php

class BreedsService{
    private Collection $breeds;
    private Collection $breedsByName;
    private Collection $showClasses;

    public function __construct(BreedsRepository $breedsRepository, ShowClassesRepository $showClassesRepository)
    {
      $this->breeds = $breedsRepository->getAll();
      $this->breedsByName = $this->breeds->groupBy("name");
      $this->showClasses = $showClassesRepository->getAll();
    }

    public function isStandardBreed(string $name): bool
    {
      return $this->breedsByName->offsetExists($name);
    }

    public function getSectionBreedNames(string $section): Collection
    {
      return $this->breeds->where("section", $section)->name;
    }

    public function getClassSelectOptionsHtml($sectionName, $selectedVariety = ""): string
    {
        $selectOptions = $this->getClassSelectOptions($sectionName);
        $optionsHtml = "";
        foreach($selectOptions as $selectOption){
          $optionSelected = ($selectedVariety == $selectOption) ? "selected" : "";
          $optionsHtml .= "<option value = '".$selectOption."' ".$optionSelected.">".$selectOption."</option>";
        }
    
        return $optionsHtml;
      }
    
      private function getClassSelectOptions(string $sectionName): array
      {
        $selectOptions = array();
        $sectionBreedNames = $this->getSectionBreedNames($sectionName);
        $showSectionClassNames = $this->showClasses->where("section", $sectionName)->groupByUniqueKey("class_name");

        foreach($sectionBreedNames as $breedName){
          if(!isset($showSectionClassNames[$breedName])){
            array_push($selectOptions, $breedName);
          }
        }
    
        return $selectOptions;
      }
}