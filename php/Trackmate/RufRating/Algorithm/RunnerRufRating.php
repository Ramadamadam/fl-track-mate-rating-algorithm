<?php

namespace Trackmate\RufRating\Algorithm;

use Trackmate\RufRating\Model\IRunner;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;


/**
 * Ruf rating per runner (comb of horse + race)
 */
class RunnerRufRating
{
    public IRunner $runner;
    public float $runnerFactor;
    public float $raceFactor;
    public float $rating;

}