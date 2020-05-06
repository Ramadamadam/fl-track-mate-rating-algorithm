<?php

namespace Trackmate\RufRating\Model;
require __DIR__ . '/../../../vendor/autoload.php';

use DateTime;
use Ds\Hashable;
use Ds\Set;

/**
 * Class RaceKey  -> A composite key which in together define a unique race
 */
class RaceKey implements Hashable
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
        return $this->track_name . ' ' . $this->race_date . ' ' . $this->race_time;
    }

    function hash()
    {
        return $this->__toString();
    }

    function equals($that): bool
    {
        return ($this->track_name == $that->track_name
            && $this->race_date == $that->race_date
            && $this->race_time == $that->race_time);
    }
}

class IRace implements Hashable
{
    public RaceKey $race_key;
    public ?string $race_type = null;
    public ?string $race_name = null;
    public ?string $race_class = null;
    public ?int $number_of_runners = null;

    public ?int $race_distance_adjusted_in_yards = null;

    function hash()
    {
        return $this->race_key->hash();
    }

    function equals($obj): bool
    {
        return $this->race_key->equals($obj->race_key);
    }

    public function is_more_than_one_runner()
    {
        return $this->number_of_runners > 1;
    }
}


class IHorse implements Hashable
{
    public string $horse_name;  //the key

    function hash()
    {
        return $this->horse_name;
    }

    function equals($obj): bool
    {
        return $this->horse_name == $obj->horse_name;
    }
}

/**
 * A combination of horse + race + other informatin.  This is a rich domain model, meaning it uses other domain models as its members
 * @package Trackmate\RufRatingRewrite\Model
 */
class IRunner implements Hashable
{
    /**
     * @var int The same as in the table
     */
    public int $id;

    public IHorse $horse;
    public IRace $race;

    public ?int $placing_numerical = null;
    public ?string $place = null;

    //not exactly the same with the value in the table
    public ?float $total_distance_beat = null;

    public static function extractHorsesAsSet(array $race_runners): Set
    {
        $horse_array = array_map(fn($runner) => $runner->horse, $race_runners);
        return new Set($horse_array);
    }

    public static function extractRacesAsSet(array $race_runners): Set
    {
        $race_array = array_map(fn($runner) => $runner->race, $race_runners);
        return new Set($race_array);
    }

    public static function filterByRaceKey(array $race_runners, RaceKey $race_key): array
    {
        return array_filter($race_runners, fn($runner) => $runner->race->race_key->equals($race_key));
    }

    public static function filterByRaceKeys(array $race_runners, array $race_keys): array
    {
        return array_filter($race_runners, fn($runner) => in_array($runner->race->race_key, $race_keys));
    }

    public static function filterByRunnerIdSet(array $race_runners, Set $runner_id_set)
    {
        return array_filter($race_runners, fn($runner) => $runner_id_set->contains($runner->id));
    }

    public static function filterByHorseName(array $race_runners, $horse_name)
    {
        return array_filter($race_runners, fn($runner) => $runner->horse->horse_name == $horse_name);
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

    function hash()
    {
        return $this->id;
    }

    function equals($obj): bool
    {
        return $this->id == $obj->id;
    }
}


?>