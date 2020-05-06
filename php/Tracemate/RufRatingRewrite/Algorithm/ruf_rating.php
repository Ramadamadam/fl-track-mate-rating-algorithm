<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use DateInterval;
use Ds\Map;
use Trackmate\RufRatingRewrite\DataAccess\RufRatingDataAccess;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;
use function Trackmate\RufRatingRewrite\DataAccess\get_table_records_by_race_key;


require_once __DIR__ . '/../Model/Models.php';

require_once __DIR__ . '/ruf_rating_result.php';
require_once __DIR__ . '/ruf_rating_race_factor_calc.php';
require_once __DIR__ . '/../DataAcess/RufRatingDataAccess.php';


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
 * @return RufRatingMiddleResult|null  Null if the race doesn't exist
 */
function get_ruf_ratings_for_race_next_day(RaceKey $race_key, DateInterval $race_dates_interval): ?RufRatingFinalResult
{

    $dataAccess = new RufRatingDataAccess();


    //get runners related to the target race
    $target_race_runners = $dataAccess->getRaceRunnersByRaceKey($race_key);
    if (empty($target_race_runners)) {
        return null;
    }

    //initiate the result object
    $ruf_rating_middle_result = new RufRatingMiddleResult();
    $ruf_rating_middle_result->target_race = $target_race_runners[array_key_first($target_race_runners)]->race;

    //get horses of the target race
    $horse_set = RaceRunner::extractHorsesAsSet($target_race_runners);


    //get these horses' runners in the period
    ////The java code's comment:  This will process the x days period up to and including yesterday (if processing ratings for tomorrow).
    $end_date = date_sub($race_key->getRaceDateAsDateType(), DateInterval::createFromDateString("2 days"));
    $start_date = clone $end_date;
    date_sub($start_date, $race_dates_interval);
    $history_races_runners_of_these_horses = $dataAccess->getRaceRunnersByHorsesBetween($start_date, $end_date, $horse_set->toArray());
    $history_races = RaceRunner::extractRacesAsSet($history_races_runners_of_these_horses)->toArray();
    ////filter races and runners by compatibility
    $history_races = filter_compatible($history_races, $ruf_rating_middle_result->target_race);
    $history_race_keys = array_map(fn($race) => $race ->race_key, $history_races);
    $history_races_runners_of_these_horses = RaceRunner::filterByRaceKeys($history_races_runners_of_these_horses, $history_race_keys);
    $history_race_runners = $dataAccess->getRaceRunnersByRaceKeys($history_race_keys);


    //calculate runner-factor for each runner
    foreach ($history_races_runners_of_these_horses as $history_race_runner) {
        $runner_factor = get_runner_factor($history_race_runner);
        if (isset($runner_factor)) {
            $ruf_rating_middle_result->putRunnerFactor($history_race_runner->id, $runner_factor);
        }
    }


    $race_runner_having_factors = RaceRunner::filterByRunnerIdSet($history_races_runners_of_these_horses, $ruf_rating_middle_result->getAllRunnerIdSet());


    //Now calculate the race factors
    //create a related race matrix for race factor calculation
    $related_race_matrix = get_related_races_matrix($history_races, $history_race_runners);
    calculate_race_factors_for_all($ruf_rating_middle_result, $history_race_runners, $related_race_matrix);

    //convert middle result to final result
    $ruf_rating_final_result = new  RufRatingFinalResult();
    $ruf_rating_final_result->target_race = $ruf_rating_middle_result->target_race;
    foreach ($race_runner_having_factors as $runner) {
        $entry = new RufRatingFinalResultEntry();
        $entry->race_runner = $runner;
        $entry->runner_factor = $ruf_rating_middle_result->getRunnerFactorByRunnerId($runner->id);
        $entry->race_factor = $ruf_rating_middle_result->getRaceFactorByRaceKey($runner->race->race_key);


        if (!isset($entry->runner_factor)  || !isset( $entry->race_factor)) {
            continue;
        }
        $entry->rating = $entry->runner_factor * $entry->race_factor;

        $ruf_rating_final_result->entries->put($runner->id, $entry);
    }



    return $ruf_rating_final_result;
}


function get_runner_factor(RaceRunner $race_runner): ?float
{
    //if not run, no rating.
    if (!$race_runner->hasRunTheRace()) {
        return null;
    }

    //weird distance from winner? no rating
    if (!$race_runner->isDistanceBeatMakingSense()) {
        return null;
    }

    if($race_runner->total_distance_beat >= 20){ //if you lose by 20, the runner is included
        return null;
    }

    if(!$race_runner->race->is_more_than_one_runner()){
        return null;
    }


//ns - nose, .03 of a length
//sh - short head, .06 of a length
//hd - head, 0.1 of a length
//nk - neck, .25 of a length

    //todo: total distance probelm:



    $feet_per_length = 8;
    $feet_per_yards = 3;

    $race_distance_in_feet = $race_runner->race->race_distance_adjusted_in_yards * $feet_per_yards;
    return $race_distance_in_feet / ($race_distance_in_feet - $race_runner->total_distance_beat * $feet_per_length);
}


/**
 * @return Map<RaceKey, RaceKey[]>
 */
function get_related_races_matrix(array $history_races, $all_race_runners): Map
{



    $result_map = new Map();

    //as you imagine, this will be a n*n calculation
    foreach ($history_races as $this_race) {

        $related_race_keys_for_this_race = []; //type: RaceKey

        foreach ($history_races as $that_race) {
            if ($this_race === $that_race) {
                continue; //One is not related to itself
            }
            if (!are_races_compatible($this_race, $that_race)) {
                continue;
            }

            $this_race_horse_set = RaceRunner::extractHorsesAsSet(RaceRunner::filterByRaceKey($all_race_runners, $this_race->race_key));
            $that_race_horse_set = RaceRunner::extractHorsesAsSet(RaceRunner::filterByRaceKey($all_race_runners, $that_race->race_key));

            if ($this_race_horse_set->intersect($that_race_horse_set)) { //if two races share a horse,  then they are related
                array_push($related_race_keys_for_this_race, $that_race->race_key);
            }
        }
        $result_map->put($this_race->race_key, $related_race_keys_for_this_race);
    }
    return $result_map;
}


/**
 * @param array|Race[] $races
 * @param Race $target_race
 * @return array|RaceRunner[]
 */
function filter_compatible(array $races, Race $target_race)
{
    return array_filter($races, fn($race) => are_races_compatible($race, $target_race));
}

function are_races_compatible(Race $this_race, Race $that_race)
{
    //TODO: For now just assume all are compatible
    return true;
}

?>


