<?php

namespace Trackmate\RufRatingRewrite\Model;


use DateTime;

/**
 * Class RaceKey  -> A composite key which in together define a unique race
 */
class RaceKey
{
    public ?string $track_name = null;
    public ?string $race_date = null;
    public ?string $race_time = null;

    public function getRaceDateAsDateType(): ?DateTime
    {
        if (isset($this->race_date)) {
            return date_create($this->race_date);
        } else {
            return null;
        }

    }

    public function __toString()
    {
        return $this->track_name .' '. $this->race_date .' '. $this->race_time;
    }

}

class Race
{
    public RaceKey $race_key;
    public ?string $race_type = null;
    public ?string $race_name = null;
    public ?string $race_class = null;
    public ?float $race_distance_furlongs = null;
}


class Horse
{
    public string $horse_name;  //the key
}

/**
 * A combination of horse + race + other informatin.  This is a rich domain model, meaning it uses other domain models as its members
 * @package Trackmate\RufRatingRewrite\Model
 */
class RaceRunner
{
    /**
     * @var int The same as in the table
     */
    public int $id;

    public Horse $horse;
    public Race $race;

    public ?int $placing_numerical = null;
    public ?string $place = null;

    public ?float $total_distance_beat = null;

    public static function getAllHorses(array $race_runners)
    {
        return array_map(fn($runner) => $runner->horse, $race_runners);
    }

    public function isDistanceBeatMakingSense(): bool
    {
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


?>