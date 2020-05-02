<?php

namespace Trackmate\RufRatingRewrite\Model;

/**
 * Class RaceKey  -> A composite key which in together define a unique race
 */
class RaceKey
{


    public ?string $race_type = null;


    public ?string $race_name = null;



    public ?string $race_class = null;



    public ?string $race_date = null;

    public ?string $race_time = null;

}


/**
 * Class HorseKey -> A composite key which in together define a unique horse
 */
class HorseKey
{
    public ?string $horse_name = null;
    public ?string $horse_type = null;
}

/**
 * Class Runner  a horse's information in a single race
 * @package Trackmate\RufRatingRewrite\Model
 */
class RaceRunner
{
    public HorseKey $horse_key;
    public ?int $placing_numerical = null;
    public ?string $place = null;
    public ?float $total_distance_beat = null;

    public function isDistanceBeatMakingSense(): bool {
        return isset($this->total_distance_beat) && $this->total_distance_beat >= 0;
    }

    /**
     * @return bool
     */
    public function hasRunTheRace(): bool
    {
        $placing_numerical_valid = $this->placing_numerical > 0;
        $place_valid = preg_match("/^[1-9]+.*/", $this->place);
        return $placing_numerical_valid && $place_valid;
    }
}


class Race
{
    public RaceKey $race_key;
    public ?float $race_distance_furlongs = null;
}
?>