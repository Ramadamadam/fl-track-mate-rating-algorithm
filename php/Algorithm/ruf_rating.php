<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use DateTime;

require_once '../Model/Models.php';
use trackmate\ruf\rating\rewrite\Model\RaceKey;
use trackmate\ruf\rating\rewrite\Model\HorseKey;

/**
 * A horse's ruf rating in a single race
 */
class RufRating
{
    public HorseKey $horseKey;
    public float $rating;
}


/**
 * Say there is a race tomorrow. Predict all horses' ruf ratings based each horse's racing record from [yesterday- days_back,  yesterday]
 * Corresponding Java code for the date range is
 *
    Date periodEndDate = RacingHelper.getOffsetDate(raceDate, -2, Calendar.DATE);
    Date periodStartDate = RacingHelper.getOffsetDate(periodEndDate, -calendarModifactionQuantity, calendarModificationUnit);
 *
 *
 * @param RaceKey $raceKey
 * @param int $days_back
 * @return array|RufRating[]
 */
function get_ruf_ratings_for_race_next_day(RaceKey $raceKey, int $days_back): array
{
    $rufRating = new RufRating();
    $rufRating->race_date_time = new DateTime();

    return [
        $rufRating
    ];
}




?>


