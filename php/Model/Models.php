<?php

namespace Trackmate\RufRatingRewrite\Model;

/**
 * Class RaceKey  -> A composite key which in together define a unique race
 */
class RaceKey
{

    /**
     * @var string|null
     */
    public $race_type;

    /**
     * @var string|null
     */
    public $race_name;


    /**
     * @var string|null
     */
    public $race_class;


    /**
     * @var string|null
     */
    public $race_date;

    /**
     * @var string|null
     */
    public $race_time;

}


/**
 * Class HorseKey -> A composite key which in together define a unique horse
 */
class HorseKey
{

    /**
     * @var string|null
     */
    public $horse_name;

    /**
     * @var string|null
     */
    public $horse_type;
}

/**
 * Class Runner  a horse's information in a single race
 * @package Trackmate\RufRatingRewrite\Model
 */
class RaceRunner
{
    public HorseKey $horse_key;
    public ?int $placing_numerical;
    public ?string $place;
    public ?float $total_distance_beat;

    public function isNotRun(): bool
    {
        return (!isset($this->placing_numerical)) || $this->placing_numerical <= 0;
    }

    public function isDistanceBeatMakingSense(): bool {
        return isset($this->total_distance_beat) && $this->total_distance_beat >= 0;
    }
}


class Race
{
    public RaceKey $race_key;
}
?>