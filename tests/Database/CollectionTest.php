<?php

// tests/ExampleTest.php
use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__).'/bootstrap.php';
// require_once '../partials/Services/PlacementsService.php';

class CollectionTest extends TestCase
{
    private Collection $testCollection;

    // The setUp method is called before each test
    protected function setUp(): void
    {
        $this->testCollection = new Collection();
        $placementModel1 = PlacementModel::createWithID(1, 1, 1, 1, 1, false);
        $this->testCollection->add($placementModel1);
        $placementModel2 = PlacementModel::createWithID(2, 2, 1, 2, 1, false);
        $this->testCollection->add($placementModel2);
        $placementModel2 = PlacementModel::createWithID(3, 3, 1, 3, 2, true);
        $this->testCollection->add($placementModel2);
    }

    public function testCollectionGet(){
        $this->assertEquals(1, $this->testCollection->get("placement", 1)->id);
        $this->assertEquals(2, $this->testCollection->get("placement", 2)->id);
    }    

    public function testCollectionWhere(){
        $this->assertEquals(2, count($this->testCollection->where("printed", false)));
        $this->assertEquals(1, $this->testCollection->where("printed", false)[0]->id);
        $this->assertEquals(2, $this->testCollection->where("printed", false)[1]->id);

        $this->assertEquals(1, count($this->testCollection->where("printed", true)));
        $this->assertEquals(3, $this->testCollection->where("printed", true)[0]->id);
    }
}