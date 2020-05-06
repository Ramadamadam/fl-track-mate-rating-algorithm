<?php


namespace Trackmate\RufRating\Algorithm;


use Ds\Map;
use Trackmate\RufRating\Model\RaceKey;

class RufRatingsRace
{
    private RaceKey $raceKey;
    private ?string $fullRaceType = null;
    private float $factor = 2;

    //horseName -> RufRatingsRunner
    private Map $runners;

    /**
     * RufRatingsRace constructor.
     * @param RaceKey $raceKey
     */
    public function __construct(RaceKey $raceKey)
    {
        $this->raceKey = $raceKey;
        $this->runners = new Map();
    }


    /**
     * @return RaceKey
     */
    public function getRaceKey(): RaceKey
    {
        return $this->raceKey;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFullRaceType(): ?string
    {
        return $this->fullRaceType;
    }

    /**
     * @param string $fullRaceType
     */
    public function setFullRaceType(?string $fullRaceType)
    {
        $this->fullRaceType = $fullRaceType;
    }

    /**
     * @return float
     */
    public function getFactor(): float
    {
        return $this->factor;
    }

    /**
     * @param float $factor
     */
    public function setFactor(float $factor): void
    {
        $this->factor = $factor;
    }


    public function addRunner(RufRatingsRunner $rufRatingsRunner)
    {
        $this->runners->put($rufRatingsRunner->getHorseName(), $rufRatingsRunner);
    }

    public function getRunners(): Map
    {
        return $this->runners;
    }




}