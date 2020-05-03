<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use DateInterval;
use Ds\Map;
use Ds\Set;
use Trackmate\RufRatingRewrite\DataAccess\PDODataAccess;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;
use function Trackmate\RufRatingRewrite\DataAccess\get_table_records_by_race_key;


require_once __DIR__ . '/../Model/Models.php';


require_once __DIR__ . '/../DataAcess/PDODataAccess.php';


class RufRatingsResult
{
    public Race $target_race;

    //type is RufRating
    public array $history_race_ratings = [];
}

/**
 * A horse's ruf rating for a single race
 */
class RufRating
{
    public RaceRunner $race_runner;
    public ?float $runner_factor = null;
    public ?float $race_factor = null;


    public function isValid(): bool
    {
        return isset($this->runner_factor); //&&  isset($this->race_factor);
    }
}



/**
 * Say there is a race tomorrow. Predict all horses' ruf ratings based each horse's racing record from [yesterday- race_dates_interval,  yesterday]
 * Corresponding Java code for the date range is
 *
 * Date periodEndDate = RacingHelper.getOffsetDate(raceDate, -2, Calendar.DATE);
 * Date periodStartDate = RacingHelper.getOffsetDate(periodEndDate, -calendarModifactionQuantity, calendarModificationUnit);
 *
 *
 * @param RaceKey $race_key
 * @param DateInterval $race_dates_interval such as "5 months"
 * @param float $length_per_furlong How many lengths are there per furlong?
 * @return RufRatingsResult|null  Null if the race doesn't exist
 */
function get_ruf_ratings_for_race_next_day(RaceKey $race_key, DateInterval $race_dates_interval, float $length_per_furlong): ?RufRatingsResult
{

    $dataAccess = new PDODataAccess();


    $target_race_runners = $dataAccess->getRaceRunnersByRaceKey($race_key);
    if (empty($target_race_runners)) {
        return null;
    }

    $ruf_ratings_result = new RufRatingsResult();
    $ruf_ratings_result->target_race = $target_race_runners[array_key_first($target_race_runners)]->race;

    $horse_set = RaceRunner::extractHorses($target_race_runners);


    //The java code's comment:  This will process the x days period up to and including yesterday (if processing ratings for tomorrow).
    $end_date = date_sub($race_key->getRaceDateAsDateType(), DateInterval::createFromDateString("2 days"));
    $start_date = clone $end_date;
    date_sub($start_date, $race_dates_interval);

    $history_races_runners = $dataAccess->getRaceRunnersByHorsesBetween($start_date, $end_date, $horse_set->toArray());
    $history_races_runners = filter_compatible($history_races_runners, $ruf_ratings_result->target_race);


    //calculate runner-factor for each runner
    foreach ($history_races_runners as $history_race_runner) {
        $ruf_rating = get_ruf_rating_for_race_runner($history_race_runner, $length_per_furlong);
        if ($ruf_rating->isValid()) {
            array_push($ruf_ratings_result->history_race_ratings, $ruf_rating);
        }
    }

    $race_runners = array_map(fn($race_rating) => $race_rating->race_runner, $ruf_ratings_result->history_race_ratings);


    //create a related race matrix for race factor calculation
    $related_race_matrix = get_related_races_matrix($race_runners);

    // calculate_race_factors_for_all($history_race_runners, $ruf_ratings_result->target_race);


    return $ruf_ratings_result;
}

function get_ruf_rating_for_race_runner(RaceRunner $race_runner, $length_per_furlong): RufRating
{


    $ruf_rating = new RufRating();
    $ruf_rating->race_runner = $race_runner;


    //if not run, no rating.
    if (!$race_runner->hasRunTheRace()) {
        return $ruf_rating;
    }

    //weird distance from winner? no rating
    if (!$race_runner->isDistanceBeatMakingSense()) {
        return $ruf_rating;
    }

    $race_distance_in_lengths = $race_runner->race->race_distance_furlongs * $length_per_furlong;
    $ruf_rating->runner_factor = $race_distance_in_lengths / ($race_distance_in_lengths - $race_runner->total_distance_beat);
    return $ruf_rating;
}


/**

 * @return Map<RaceKey, RaceKey[]>
 */
function get_related_races_matrix(array $race_runners): Map
{


    $race_set = RaceRunner::extractRaces($race_runners);

    $result_map = new Map();

    //as you imagine, this will be a n*n calculation
    foreach ($race_set as $this_race) {

        $related_race_keys_for_this_race = []; //type: RaceKey

        foreach ($race_set as $that_race) {
            if ($this_race === $that_race) {
                continue; //One is not related to itself
            }
            if (!are_races_compatible($this_race, $that_race)) {
                continue;
            }

            $this_race_horse_set = RaceRunner::extractHorses(RaceRunner::filterByRaceKey($race_runners, $this_race -> race_key));
            $that_race_horse_set = RaceRunner::extractHorses(RaceRunner::filterByRaceKey($race_runners, $that_race -> race_key));

            if($this_race_horse_set->intersect($that_race_horse_set)){ //if two races share a horse, they are related
                array_push($related_race_keys_for_this_race, $that_race->race_key);
            }
        }
        $result_map->put($this_race->race_key, $related_race_keys_for_this_race);
    }
    return $result_map;
}


/**
 * @param array|RaceRunner[] $race_runners
 * @param Race $target_race
 * @return array|RaceRunner[]
 */
function filter_compatible(array $race_runners, Race $target_race)
{
    return array_filter($race_runners, fn($race_runner) => are_races_compatible($race_runner->race, $target_race));
}

function are_races_compatible(Race $this_race, Race $that_race)
{
    //TODO: For now just assume all are compatible
    return true;
}

?>


