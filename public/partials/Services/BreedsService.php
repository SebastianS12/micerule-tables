<?php

class BreedsService{
    private BreedsRepository $breedsRepository;
    private int $locationID;

    public function __construct(BreedsRepository $breedsRepository, int $locationID)
    {
        $this->breedsRepository = $breedsRepository;   
        $this->locationID = $locationID;
    }
    public function getSectionBreedNames(string $section): array|null{
        return $this->breedsRepository->getSectionBreedNames($section);
    }

    public function getClassSelectOptionsHtml($sectionName, $selectedVariety = ""){
    
        $selectOptions = $this->getClassSelectOptions($sectionName);
        $optionsHtml = "";
        foreach($selectOptions as $selectOption){
          $optionSelected = ($selectedVariety == $selectOption) ? "selected" : "";
          $optionsHtml .= "<option value = '".$selectOption."' ".$optionSelected.">".$selectOption."</option>";
        }
    
        return $optionsHtml;
      }
    
      private function getClassSelectOptions($sectionName){
        $selectOptions = array();
        $sectionBreedNames = $this->getSectionBreedNames($sectionName);
        $showSectionRepository = new ShowSectionRepository($this->locationID);
        $showSectionClassNames = $showSectionRepository->getShowSectionClassNames($sectionName);
        if($sectionBreedNames != null){
          foreach($sectionBreedNames as $breedName){
            if(!in_array($breedName, $showSectionClassNames)){
              array_push($selectOptions, $breedName);
            }
          }
        }
    
        return $selectOptions;
      }
}