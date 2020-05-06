<?php


namespace Trackmate\RufRating\Algorithm;


class RufRatingsRace
{
    private int $id;
    private string $fullRaceType;
    private float $factor;
    private RaceKey $raceKey;


    /**
     * @return RaceKey
     */
    public function getRaceKey(): RaceKey
    {
        return $this->raceKey;
    }

    /**
     * RufRatingsRace constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
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
    public function getFullRaceType(): string
    {
        return $this->fullRaceType;
    }

    /**
     * @param string $fullRaceType
     */
    public function setFullRaceType(string $fullRaceType): void
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




//    public void addRunner(IRunner runner) {
//}
//
//    public Map<Long, RufRatingsRunner> getRunners() {
//        return  null;
//    }


}