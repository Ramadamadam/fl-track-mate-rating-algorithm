<?php
namespace Trackmate\RufRatingRewrite\Algorithm;

use DateTime;

/**
 * The result data structure
 */
class RufRating
{
    public string $horse_name;
    public string $race_name;
    public DateTime $race_date_time;
    public float $rating;
}


/**
 * @return array:  <RufRating>
 */
function get_horse_ruf_ratings(): array
{
    $rufRating = new RufRating();
    $rufRating->race_date_time = new DateTime();

    return [
        $rufRating
    ];
}

?>


