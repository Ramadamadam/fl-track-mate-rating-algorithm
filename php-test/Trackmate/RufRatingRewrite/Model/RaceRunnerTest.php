<?php

namespace Trackmate\RufRatingRewrite\Model;

require_once  '/Users/kent/dev/flws/fl-track-mate-rating-algorithm/php/Tracemate/RufRatingRewrite/Model/Models.php';

use PHPUnit\Framework\TestCase;

class RaceRunnerTest extends TestCase
{

    public function testHasRunTheRace()
    {
        $this->assertTrue($this->buildRunnerByPlace(1, "99th") -> hasRunTheRace());
        $this->assertTrue($this->buildRunnerByPlace(9, "99th") -> hasRunTheRace());

        $this->assertFalse($this->buildRunnerByPlace(0, "99th") -> hasRunTheRace());
        $this->assertFalse($this->buildRunnerByPlace(-1, "99th") -> hasRunTheRace());
        $this->assertFalse($this->buildRunnerByPlace(null, "99th") -> hasRunTheRace());



        $this->assertFalse($this->buildRunnerByPlace(9, "0th") -> hasRunTheRace());
        $this->assertFalse($this->buildRunnerByPlace(9, "RR") -> hasRunTheRace());
        $this->assertFalse($this->buildRunnerByPlace(9, "RR 1th") -> hasRunTheRace());
        $this->assertFalse($this->buildRunnerByPlace(9, null) -> hasRunTheRace());

    }

    public function testIsDistanceBeatMakingSense()
    {
        $this->assertFalse($this->buildRunnerByDistanceBeat("-1") -> isDistanceBeatMakingSense());
        $this->assertFalse($this->buildRunnerByDistanceBeat("-0.5") -> isDistanceBeatMakingSense());
        $this->assertTrue($this->buildRunnerByDistanceBeat("0") -> isDistanceBeatMakingSense());
        $this->assertTrue($this->buildRunnerByDistanceBeat("0.5") -> isDistanceBeatMakingSense());
        $this->assertTrue($this->buildRunnerByDistanceBeat("1") -> isDistanceBeatMakingSense());
        $this->assertFalse($this->buildRunnerByDistanceBeat(null) -> isDistanceBeatMakingSense());

    }


    private function buildRunnerByPlace(?int $placing_numerical, ?string $place): RaceRunner
    {
        $runner = new RaceRunner();
        $runner->placing_numerical = $placing_numerical;
        $runner->place = $place;
        return $runner;
    }

    private function buildRunnerByDistanceBeat(?float $distance): RaceRunner
    {
        $runner = new RaceRunner();
        $runner->total_distance_beat = $distance;
        return $runner;
    }

}
