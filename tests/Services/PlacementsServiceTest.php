<?php

// tests/ExampleTest.php
use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__).'/bootstrap.php';
// require_once '../partials/Services/PlacementsService.php';

class PlacementsServiceTest extends TestCase
{
    private EntryModel $testEntry;
    private Collection $classPlacementCollection;

    // The setUp method is called before each test
    protected function setUp(): void
    {
        // Initialize or set up the resources needed for the tests
        $this->testEntry = EntryModel::create(1, 1, "Test", false, false, false);

        
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

    public function testEntryInPlacements()
    {
        $this->assertTrue(PlacementsService::entryInPlacements($this->testEntry, Prize::STANDARD));
        $this->assertFalse(PlacementsService::entryInPlacements($this->testEntry, Prize::GRANDCHALLENGE));
    }

    public function testEntryHasPlacements()
    {
        $this->assertTrue(PlacementsService::entryHasPlacement($this->testEntry, Prize::STANDARD, 1));
        $this->assertFalse(PlacementsService::entryHasPlacement($this->testEntry, Prize::STANDARD, 2));
        $this->assertTrue(PlacementsService::entryHasPlacement($this->testEntry, Prize::SECTION, 2));
        $this->assertFalse(PlacementsService::entryHasPlacement($this->testEntry, Prize::SECTION, 1));
        $this->assertFalse(PlacementsService::entryHasPlacement($this->testEntry, Prize::GRANDCHALLENGE, 1));
        $this->assertFalse(PlacementsService::entryHasPlacement($this->testEntry, Prize::GRANDCHALLENGE, 2));
    }

    public function testPlacementExists()
    {
        $this->assertTrue(PlacementsService::placementExists($this->classPlacementCollection, 1));
        $this->assertTrue(PlacementsService::placementExists($this->classPlacementCollection, 3));
        $this->assertFalse(PlacementsService::placementExists($this->classPlacementCollection, 2));
    }
}