<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use DateInterval;
use Trackmate\RufRatingRewrite\DataAccess\PDODataAccess;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;
use function Trackmate\RufRatingRewrite\DataAccess\get_table_records_by_race_key;


require_once __DIR__ . '/../Model/Models.php';


require_once __DIR__ . '/../DataAcess/PDODataAccess.php';


class RufRatingsResult
{
    public ?Race $target_race = null;
    public ?array $history_race_ratings = [];
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
 *  A race's related races
 *
 */
class RelatedRaces
{
    public RaceKey $race_key;
    /**
     * One is not related to itself
     * @var array | RaceKey[]
     */
    public array $related_race_keys = [];
}

/**
 * An array of RelatedRaces: All races and their related races
 *
 */
class RelatedRaceMatrix
{
    private array $array_of_related_races = [];  //array type is RelatedRaces

    public function addOne(RelatedRaces $related_races): void
    {
        array_push($this->array_of_related_races, $related_races);
    }

    /**
     * @param RaceKey $this_race_key
     * @return array|RaceKey[]  Returns [] if not found
     */
    public function getRelatedRaceKeys(RaceKey $this_race_key): array
    {
        $entry = current(array_filter($this->array_of_related_races, fn($related_race) => $this_race_key->isEqualTo($related_race->race_key)));
        if ($entry) {
            return $entry->related_race_keys;
        } else {
            return [];
        }
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
 * @return RufRatingsResult
 */
function get_ruf_ratings_for_race_next_day(RaceKey $race_key, DateInterval $race_dates_interval, float $length_per_furlong): RufRatingsResult
{

    $dataAccess = new PDODataAccess();

    $ruf_ratings_result = new RufRatingsResult();

    $target_race_runners = $dataAccess->getRaceRunnersByRaceKey($race_key);
    if (empty($target_race_runners)) {
        return $ruf_ratings_result;
    }


    $ruf_ratings_result->target_race = $target_race_runners[array_key_first($target_race_runners)]->race;

    $horses = RaceRunner::getAllHorses($target_race_runners);


    //The java code's comment:  This will process the x days period up to and including yesterday (if processing ratings for tomorrow).
    $end_date = date_sub($race_key->getRaceDateAsDateType(), DateInterval::createFromDateString("2 days"));
    $start_date = clone $end_date;
    date_sub($start_date, $race_dates_interval);

    $history_races_runners = $dataAccess->getRaceRunnersByHorsesBetween($start_date, $end_date, $horses);
    $history_races_runners = filter_compatible($history_races_runners, $ruf_ratings_result->target_race);

    //calculate runner-factor for each runner
    foreach ($history_races_runners as $history_race_runner) {
        $ruf_rating = get_ruf_rating_for_race_runner($history_race_runner, $length_per_furlong);
        array_push($ruf_ratings_result->history_race_ratings, $ruf_rating);
    }


    //create a related race matrix for race factor calculation
    // $related_race_matrix = get_related_races_for_all_races($history_races_runners);

    // calculate_race_factors_for_all($history_race_runners, $ruf_ratings_result->target_race);

    //filter out invalid ones
    $ruf_ratings_result->history_race_ratings = array_filter($ruf_ratings_result->history_race_ratings, fn($rating) => $rating->isValid());
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
 * @param array|Race[] $history_races
 * @return RelatedRaceMatrix
 */
function get_related_races_for_all_races(array $history_races): RelatedRaceMatrix
{
    $matrix = new RelatedRaceMatrix();

    //as you imagine, this will be a n*n calculation
    foreach ($history_races as $this_race) {

        $related_races_for_single_race = new  RelatedRaces();
        $related_races_for_single_race->race_key = $this_race->race_key;
        $matrix->addOne($related_races_for_single_race);

        foreach ($history_races as $that_race) {
            if ($this_race === $that_race) {
                continue; //One is not related to itself
            }
            if (!are_races_compatible($this_race, $that_race)) {
                continue;
            }
            array_push($related_races_for_single_race->related_race_keys, $that_race->race_key);
        }
    }

    return $matrix;
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


