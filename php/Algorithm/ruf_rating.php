<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use DateTime;


require_once __DIR__.'/../Model/Models.php';
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\HorseKey;


require_once __DIR__.'/../DataAcess/PDODataAccess.php';
use function Trackmate\RufRatingRewrite\DataAccess\get_table_records_by_race_key;



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

    $thisRaceTableRecords =   get_table_records_by_race_key($race_key);

    $rufRating = new RufRating();
    $rufRating->race_date_time = new DateTime();

    return [
        $rufRating
    ];
}




?>


