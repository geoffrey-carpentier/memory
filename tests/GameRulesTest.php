<?php

namespace Tests;

use App\Services\GameRules;
use PHPUnit\Framework\TestCase;

class GameRulesTest extends TestCase
{
    public function testGridDimensions(): void
    {
        $this->assertEquals([3, 2], GameRules::getGridDimensions(3));
        $this->assertEquals([4, 4], GameRules::getGridDimensions(8));
    }

    public function testScoreCalculation(): void
    {
        $score = GameRules::computeScore(4, 2, 30, 4);
        $this->assertGreaterThan(0, $score);
    }
}
