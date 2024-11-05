<?php

use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__).'/bootstrap.php';

class JudgeFormatterTest extends TestCase
{
    private Collection $judgeCollection;

    protected function setUp(): void
    {
        $this->judgeCollection = new Collection();
        $judgeModel1 = JudgeModel::createWithID(1, 1, 1, "Judge1");
        $this->judgeCollection->add($judgeModel1);
        $judgeModel2 = JudgeModel::createWithID(1, 2, 2, "Judge2");
        $this->judgeCollection->add($judgeModel2);
        $judgeModel3 = JudgeModel::createWithID(1, 3, 3, "Judge3");
        $this->judgeCollection->add($judgeModel3);
    }

    public function testCollectionGet(){
        $this->assertEquals("Judge1, Judge2, Judge3", JudgeFormatter::getJudgesString($this->judgeCollection));
    }    
}