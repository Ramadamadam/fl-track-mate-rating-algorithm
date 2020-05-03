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
    public ?array $relatedRaceRatings = [];
}

/**
 * A horse's ruf rating in a single race among related races
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

    $races_horses_in = $dataAccess->getRaceRunnersByHorsesBetween($start_date, $end_date, $horses);

    $related_race_runners = filter_compatiple($races_horses_in, $ruf_ratings_result->target_race);

    //calculate for each runner
    foreach ($related_race_runners as $related_race_runner) {
        $ruf_rating = get_ruf_rating_for_race_runner($related_race_runner, $length_per_furlong);
        if ($ruf_rating->isValid()) {
            array_push($ruf_ratings_result->relatedRaceRatings, $ruf_rating);
        }
    }

    //filter out invalid ones
    $ruf_ratings_result->relatedRaceRatings = array_filter($ruf_ratings_result->relatedRaceRatings, fn($rating) => $rating->isValid());

    // calculate_race_factors_for_all($related_race_runners, $ruf_ratings_result->target_race);


    return $ruf_ratings_result;
}

function get_ruf_rating_for_race_runner(RaceRunner $related_race_runner, $length_per_furlong): RufRating
{


    $ruf_rating = new RufRating();
    $ruf_rating->race_runner = $related_race_runner;


    //if not run, no rating.
    if (!$related_race_runner->hasRunTheRace()) {
        return $ruf_rating;
    }

    //weird distance from winner? no rating
    if (!$related_race_runner->isDistanceBeatMakingSense()) {
        return $ruf_rating;
    }

    $race_distance_in_lengths = $related_race_runner->race->race_distance_furlongs * $length_per_furlong;
    $ruf_rating->runner_factor = $race_distance_in_lengths / ($race_distance_in_lengths - $related_race_runner->total_distance_beat);
    return $ruf_rating;
}




/**
 * @param array|RaceRunner[] $race_runners
 * @param Race $target_race
 * @return array|RaceRunner[]
 */
function filter_compatiple(array $race_runners, Race $target_race)
{
    //TODO: do the filtering. For now just assume all are compatible
    return $race_runners;
}

?>


