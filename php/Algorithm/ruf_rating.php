<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use Trackmate\RufRatingRewrite\DataAccess\RaceTableRecord;
use Trackmate\RufRatingRewrite\Model\HorseKey;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;
use function Trackmate\RufRatingRewrite\DataAccess\get_table_records_by_race_key;


require_once __DIR__ . '/../Model/Models.php';


require_once __DIR__ . '/../DataAcess/PDODataAccess.php';


/**
 * A horse's ruf rating in a single race
 */
class RufRating
{
    public HorseKey $horse_key;
    public float $race_runner_factor;
}


/**
 * Say there is a race tomorrow. Predict all horses' ruf ratings based each horse's racing record from [yesterday- days_back,  yesterday]
 * Corresponding Java code for the date range is
 *
 * Date periodEndDate = RacingHelper.getOffsetDate(raceDate, -2, Calendar.DATE);
 * Date periodStartDate = RacingHelper.getOffsetDate(periodEndDate, -calendarModifactionQuantity, calendarModificationUnit);
 *
 *
 * @param RaceKey $race_key
 * @param int $days_back
 * @return array|RufRating[]
 */
function get_ruf_ratings_for_race_next_day(RaceKey $race_key, int $days_back): array
{

    $this_race_table_records = get_table_records_by_race_key($race_key);
    $this_race_runners = RaceTableRecord::extractRaceRunnersOfSingleRace($this_race_table_records);

    $ratings = [];
    //calculate for each runner
    foreach ($this_race_runners as $race_runner) {
        $ruf_rating = get_ruf_rating_for_race_runner($race_runner);
        array_push($ratings, $ruf_rating);
    }
    return $ratings;
}

function get_ruf_rating_for_race_runner(RaceRunner $race_runner): RufRating
{
    $ruf_rating = new RufRating();
    $ruf_rating->horse_key = $race_runner->horse_key;
    $ruf_rating->race_runner_factor = 0.5;
    return $ruf_rating;
}


?>


