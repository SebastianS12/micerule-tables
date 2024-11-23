<?php

class ShowClassesController{
    public static function prepareViewModel(int $locationID, ShowClassesService $showClassesService): ShowClassesViewModel
    {
        return $showClassesService->prepareViewModel($locationID);
    }

    public function deleteClass(int $classID, int $locationID, string $sectionName): WP_REST_Response
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $showClassesService = new ShowClassesService($locationID);
        $showClassesService->delete($classID, $showClassesRepository);
        $showClassesService->updateSectionPositions($sectionName, $showClassesRepository);
        $this->updateIndices($locationID);

        return new WP_REST_Response(ShowOptionsView::getSectionTablesHtml($locationID));
    }

    public function updateIndices(int $locationID): void
    {
        $indicesService = new IndicesService();
        $indicesService->updateIndices($locationID);
    }

    public function addClass(int $locationID, string $className, string $section): WP_REST_Response
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $showClassesService = new ShowClassesService();
        $showClassesService->addClass($locationID, $className, $section, $showClassesRepository);
        $this->updateIndices($locationID);

        return new WP_REST_Response(ShowOptionsView::getSectionTablesHtml($locationID));
    }

    public function swapClasses(int $locationID, int $firstClassID, int $secondClassID): WP_REST_Response
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $showClassesService = new ShowClassesService();
        $showClassesService->swapClasses($firstClassID, $secondClassID, $showClassesRepository);
        $indicesService = new IndicesService();
        $classIndexRepository = new ClassIndexRepository($locationID);
        $indicesService->swapClassIndices($firstClassID, $secondClassID, $showClassesRepository, $classIndexRepository);

        return new WP_REST_Response(ShowOptionsView::getSectionTablesHtml($locationID));
    }

    public function getClassSelectOptionsHtml(string $sectionName, int $locationID): WP_REST_Response
    {
        $breedsService = new BreedsService(new BreedsRepository, new ShowClassesRepository($locationID));

        return new WP_REST_Response($breedsService->getClassSelectOptionsHtml($sectionName));
    }
}