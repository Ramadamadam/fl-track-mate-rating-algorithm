<?php

namespace Trackmate\RufRatingRewrite\Model;

/**
 * Class RaceKey  -> A composite key which in together define a unique race
 */
class RaceKey
{

    /**
     * @var string|null
     */
    public $race_type;

    /**
     * @var string|null
     */
    public $race_name;


    /**
     * @var string|null
     */
    public $race_class;


    /**
     * @var string|null
     */
    public $race_date;

    /**
     * @var string|null
     */
    public $race_time;

}


/**
 * Class HorseKey -> A composite key which in together define a unique horse
 */
class HorseKey
{

    /**
     * @var string|null
     */
    public $horse_name;

    /**
     * @var string|null
     */
    public $horse_type;
}

?>