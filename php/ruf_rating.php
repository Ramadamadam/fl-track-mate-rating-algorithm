<?php

/**
 * The result data structure
 */
class RufRating
{
    public string $horse_name;
    public string $race_name;
    public DateTime $race_date;
    public float $rating;
}


/**
 * @return array:  <DateTime -> RufRating>
 */
function get_horse_ruf_ratings(): array
{
    $rufRating = new RufRating();

    return $rufRating;
}

?>


