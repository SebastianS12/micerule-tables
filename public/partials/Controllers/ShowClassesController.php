<?php

class ShowClassesController{
    public static function prepareViewModel(int $locationID, ShowClassesService $showClassesService): ShowClassesViewModel
    {
        return $showClassesService->prepareViewModel($locationID);
    }

    public static function deleteClass(int $classID, int $locationID, string $sectionName): void
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $showClassesService = new ShowClassesService($locationID);
        $showClassesService->delete($classID, $showClassesRepository);
        $showClassesService->updateSectionPositions($sectionName, $showClassesRepository);

        self::updateIndices($locationID);
    }

    public static function updateIndices(int $locationID): void
    {
        $indicesService = new IndicesService();
        $indicesService->updateIndices($locationID);
    }

    public static function addClass(int $locationID, string $className, string $section): void
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $showClassesService = new ShowClassesService();
        $showClassesService->addClass($locationID, $className, $section, $showClassesRepository);
        self::updateIndices($locationID);
    }

    public static function swapClasses(int $locationID, int $firstClassID, int $secondClassID): void
    {
        $showClassesRepository = new ShowClassesRepository($locationID);
        $showClassesService = new ShowClassesService();
        $showClassesService->swapClasses($firstClassID, $secondClassID, $showClassesRepository);
        $indicesService = new IndicesService();
        $classIndexRepository = new ClassIndexRepository($locationID);
        $indicesService->swapClassIndices($firstClassID, $secondClassID, $showClassesRepository, $classIndexRepository);
    }
}