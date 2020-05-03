<?php

namespace Trackmate\RufRatingRewrite\Algorithm;

use Ds\Map;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;


class RufRatingFinalResult
{
    public Race $target_race;

    //<RaceRunner.Id, RufRatingFinalResultEntry>
    public Map $entries;

    public function __construct()
    {
        $this->entries = new Map();
    }
}

class RufRatingFinalResultEntry
{
    public RaceRunner $race_runner;
    public float $runner_factor;
    public float $race_factor;
    public float $rating;
}


class RufRatingMiddleResult
{
    public Race $target_race;

    //<RaceRunner's id, float value>
    private Map $runner_factor_map;

    //<RaceKey, float value>
    private Map $race_factor_map;

    public function __construct()
    {
        $this->runner_factor_map = new Map();
        $this->race_factor_map = new Map();
    }

    public function putRunnerFactor(int $runner_id, float $factor)
    {
        $this->runner_factor_map->put($runner_id, $factor);
    }

    public function putRaceFactor(RaceKey $race_key, float $factor)
    {
        $this->race_factor_map->put($race_key, $factor);
    }

    public function getAllRunnerIdSet()
    {
        return $this->runner_factor_map->keys();
    }

    /**
     * @return Map
     */
    public function getRaceFactorMap(): Map
    {
        return $this->race_factor_map;
    }

    public function getRaceFactorByRaceKey(RaceKey $race_key): ?float
    {
        return $this->race_factor_map->get($race_key);
    }


    public function toFindResult(): RufRatingFinalResult
    {
        $finalResult = new RufRatingFinalResult();
        $finalResult->target_race = $this->target_race;

    }

    public function getRunnerFactorByRunnerId($runner_id): ?float
    {
        return $this->runner_factor_map->get($runner_id);
    }
}