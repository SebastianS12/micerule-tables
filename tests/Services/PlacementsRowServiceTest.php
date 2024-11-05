<?php

use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__).'/bootstrap.php';

class PlacementsRowServiceTest extends TestCase
{
    private PlacementsRowService $placementsRowService;
    private EntryModel $testEntry;
    private Collection $classPlacementCollection;

    // The setUp method is called before each test
    protected function setUp(): void
    {
        $this->placementsRowService = new PlacementsRowService();
        // Initialize or set up the resources needed for the tests
        $this->testEntry = EntryModel::createWithID(1, 1, 1, "Test", false, false, false);

        
        $placementCollection = new Collection();
        $placement1 = PlacementModel::create(1, 1, 1, Prize::STANDARD->value, false);
        $placementCollection->add($placement1);
        $placement2 = PlacementModel::create(1, 1, 2, Prize::SECTION->value, false);
        $placementCollection->add($placement2);

        $this->classPlacementCollection = new Collection();
        $this->classPlacementCollection->add($placement1);
        $classPlacement2 = PlacementModel::create(2, 1, 3, Prize::STANDARD->value, false);
        $this->classPlacementCollection->add($classPlacement2);

        $this->testEntry->setRelation("placements", $placementCollection);
    }

    public function testNoOtherPlacementsForEntry(){
        $reflection = new ReflectionClass($this->placementsRowService);
        $method = $reflection->getMethod("noOtherPlacementsForEntry");
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->placementsRowService, $this->classPlacementCollection, 1, $this->testEntry));
        $this->assertFalse($method->invoke($this->placementsRowService, $this->classPlacementCollection, 2, $this->testEntry));
        $this->assertFalse($method->invoke($this->placementsRowService, $this->classPlacementCollection, 3, $this->testEntry));
    }    
}