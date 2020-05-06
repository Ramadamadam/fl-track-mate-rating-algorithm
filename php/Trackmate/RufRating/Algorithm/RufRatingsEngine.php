<?php


namespace Trackmate\RufRating\Algorithm;


require_once __DIR__ . '/../DataAccess/RufRatingDataAccess.php';

require_once __DIR__ . '/RufRatingsRace.php';
require_once __DIR__ . '/RufRatingsRunner.php';

use DateInterval;
use DateTime;
use Ds\Map;
use Trackmate\RufRating\DataAccess\RufRatingDataAccess;
use Trackmate\RufRating\Model\IRace;
use Trackmate\RufRating\Model\IRunner;


/** The number of iterative refinements performed when calculating race factors. */
define("RACE_RATINGS_NUM_ITERATIONS", 5);
/** The initial increment tick when calculating the race ratings. */
define("RACE_RATINGS_START_INCREMENT_SIZE", 0.25);
/** The multiplier for the race factor increment tick for each iteration. */
define("RACE_RATINGS_INCREMENT_REDUCTION_FACTOR", 0.1);
/** The multiplier which when combined with the increment tick dictates the range of permutations of the race factor per generation. */
define("RACE_RATINGS_INCREMENT_MULTIPLIER", 2);

class RufRatingsEngine
{


    /**
     *
     *
     * @param DateInterval $raceDatesInterval such as "5 months" or "100 days"
     * @param debug if true, echo debug information on page
     */
    public function processRacesForDate(DateTime $raceDate, DateInterval $raceDatesInterval, bool $debug)
    {
        // This will process the x month period up to and including yesterday (if processing ratings for tomorrow).

        $dataAccess = new RufRatingDataAccess();

        $periodEndDate = date_sub($raceDate, DateInterval::createFromDateString("2 days"));
        $periodStartDate = clone $periodEndDate;
        date_sub($periodStartDate, $raceDatesInterval);

        $periodRunners = $dataAccess->getRunnersBetween($periodStartDate, $periodEndDate);
        $periodRaces = IRunner::extractRacesAsSet($periodRunners)->toArray();

        if ($debug) {
            echo "Found " . count($periodRaces) . " Races to process between " . $periodStartDate->format('Y-m-d') . " and " . $periodEndDate->format('Y-m-d') . ".";
        }


        // Get the runner factors and RufRatingsRaces.
        $runnerFactors = new Map();  //runner id -> float value
        $ratingsRaces = new Map(); // Long, RufRatingsRace // race key -> RufRatingsRace

        $this->getRunnerFactorsAndRatingsRaces($periodRaces, $periodRunners, $runnerFactors, $ratingsRaces);

        if ($debug) {
            echo "<p>The runner factors are calculated </p>";
//            echo "<pre>";
//            var_dump($runnerFactors);
//            echo "</pre>";
        }

        // Build up all related races.
        //// raceKey => array of related race keys
        $relatedRaceKeys = new Map();
        // horseName => array of RufRatingsRace
        $horseRaceRatings = new Map();
        $this->getRelatedRaces($ratingsRaces, $relatedRaceKeys, $horseRaceRatings);


        //////
        // Calculate the RaceFactors
        //////
        if ($debug) {
            echo "Calculating race factors for " . count($ratingsRaces) . " Races.";
        }
        $this->calculateRaceFactors($ratingsRaces, $relatedRaceKeys, $debug);

        if ($debug) {
            echo "Race factor calculation complete (100%)";
        }


//
//    //////
//    // Store the ratings.
//    //////
//    try {
//        storeRatings(racesForDate, ratingsTypeId, ratingsRaceValidityChecker, horseRaceRatings, ratingsRaces, racingService, ratingsService,
//            debug);
//    } catch (Exception e) {
//    logger.error("Failed to store ratings for date: " + raceDate, e);
//    //marketUnsuccessfulDates.add(raceDate);
//    return;
//}
//     return null;
    }


    /**
     * Get the runner factors and RufRatingsRaces for the supplied Races.
     * @param periodRaces The collection of Races to iterate over to get the runner factors and RufRatingsRaces from.
     * @param $periodRunners all runners in the period
     * @param runnerFactors The map to insert the runner factors into (keyed on runner.id).
     * @param ratingsRaces The map to insert the RufRatingsRaces into (keyed on race key).
     */
    private function getRunnerFactorsAndRatingsRaces(array $periodRaces, array $periodRunners, Map $runnerFactors, Map $ratingsRaces)
    {

        /** @var $periodRace IRace */
        foreach ($periodRaces as $periodRace) {
            // Build up a RufRatingsRace.
            $ratingsRace = new RufRatingsRace($periodRace->race_key);
            $fullRaceType = $periodRace->race_type;
            $ratingsRace->setFullRaceType($fullRaceType);


            // Get all runners for the race.
            $raceRunners = IRunner::filterByRaceKey($periodRunners, $periodRace->race_key);

            /** @var $raceRunner IRunner */
            foreach ($raceRunners as $raceRunner) {
                // Ignore non-runners.
                if (!$raceRunner->hasRunTheRace()) {
                    continue;
                }

                // Ignore runners with no result.
                if (!$raceRunner->isDistanceBeatMakingSense()) {
                    continue;
                }

                if ($raceRunner->total_distance_beat >= 20) { //if you lose by 20, the runner is included
                    continue;
                }

                if (!$raceRunner->race->is_more_than_one_runner()) {
                    continue;
                }


                $feet_per_length = 8;
                $feet_per_yards = 3;

                $race_distance_in_feet = $raceRunner->race->race_distance_adjusted_in_yards * $feet_per_yards;
                $runnerFactor = $race_distance_in_feet / ($race_distance_in_feet - $raceRunner->total_distance_beat * $feet_per_length);
                $runnerFactors->put($raceRunner->id, $runnerFactor);

                // Build up a RufRatingsRunner.
                $ratingsRunner = new RufRatingsRunner();
                $ratingsRunner->setDate($periodRace->race_key->getRaceDateAsDateType());
                $ratingsRunner->setHorseName($raceRunner->horse->horse_name);
                $ratingsRunner->setRating($runnerFactor);
                $ratingsRace->addRunner($ratingsRunner);
            }


            if (count($ratingsRace->getRunners()) > 0) {
                $ratingsRaces->put($ratingsRace->getRaceKey(), $ratingsRace);
            }
        }
    }

    /**
     *  Build up the map of related races for each race. Also build up the map of races each horse was involved in.
     * @param $ratingsRaces The map of RufRatingsRaces, keyed on race ID.
     * @param $relatedRatingsRaceMap The map of Collections of RufRatingsRaces, keyed on race key.   Jian: output parameter
     * @param $horseRaceRatingsMap The map of Collections of RufRatingsRaces, keyed on horse ID. Jian: output parameter
     */
    private function getRelatedRaces(Map $ratingsRaces, Map $relatedRatingsRaceMap, Map $horseRaceRatingsMap)
    {
        // horse name -> array of Race keys
        $horseAndTheirRaceKeys = new Map(); //Jian: horse and the races they have been really in
        // Get a map of all races each horse has been in.
        /**  @var $ratingsRace RufRatingsRace */
        foreach ($ratingsRaces->values() as $ratingsRace) {
            //horse name => RufRatingsRunner
            $rufRatingsRunnersMap = $ratingsRace->getRunners(); //key: horseName
            foreach ($rufRatingsRunnersMap->keys() as $horseName) {
                $horseRaceKeys = $horseAndTheirRaceKeys->get($horseName, null);
                if ($horseRaceKeys == null) {
                    $horseRaceKeys = [];
                    $horseAndTheirRaceKeys->put($horseName, $horseRaceKeys);
                }
                array_push($horseRaceKeys, $ratingsRace->getRaceKey());
            }
        }

        /** @var  $ratingsRace RufRatingsRace */
        foreach ($ratingsRaces->values() as $ratingsRace) {
            $relatedRaces = $relatedRatingsRaceMap->get($ratingsRace->getRaceKey(), null);  //Jian: initialise the the map of relatedRaceIds - start
            if ($relatedRaces == null) {
                $relatedRaces = [];
                $relatedRatingsRaceMap->put($ratingsRace->getRaceKey(), $relatedRaces);
            } //Jian: initialise the the map of relatedRaceIds - end

            /** @var  $ratingsRunner RufRatingsRunner */
            foreach ($ratingsRace->getRunners()->values() as $ratingsRunner) { //Jian: for each race, loop thru its runners
                $raceKeys = $horseAndTheirRaceKeys->get($ratingsRunner->getHorseName()); //Jian: get all races this horse has been in
                foreach ($raceKeys as $raceKey) {
                    if (!$raceKey->equals($ratingsRace->getRaceKey()) //Jian:  not the same race
                        && !$relatedRaces->contains($raceKey)) { //Jian: the race hasn't been included to result map yet
                        // If the 2 races are compatible then make a note of the relationship.
                        $thisRaceType = $ratingsRace->getFullRaceType();
                        $relatedRaceType = $ratingsRaces->get($raceKey)->getFullRaceType();
                        if ($this->isCompatibleRaceType($thisRaceType, $relatedRaceType)) {
                            array_push($relatedRaces, $raceKey);
                        }
                    }
                }
            }

            // If this race has related races, then make a note that all the horses ran in this race->
            if (count($relatedRaces) > 0) {

                foreach ($ratingsRace->getRunners()->values() as $ratingsRunner) {
                    $horseRaceKeys = $horseRaceRatingsMap->get($ratingsRunner->getHorseName());
                    if ($horseRaceKeys == null) {
                        $horseRaceKeys = [];
                        $horseRaceRatingsMap->put($ratingsRunner->getHorseName(), $horseRaceKeys);
                    }
                    $horseRaceKeys->add($ratingsRace);
                }
            }
        }

    }


    /**
     * Calculate the Race factors for each Race in the period.
     * @param $ratingsRaces The Map of RudRatingsRace, keyed on Race Key.  //Jian: both input and output
     * @param $relatedRatingRaceMap The Map of Collections of related Race Key, keyed on Race Key.
     * @param $debug
     */
    private function calculateRaceFactors(Map $ratingsRaces, Map $relatedRatingRaceMap, bool $debug)
    {

        $incrementSize = RACE_RATINGS_START_INCREMENT_SIZE;
        for ($iteration = 0; $iteration < RACE_RATINGS_NUM_ITERATIONS; $iteration++) {
            if ($debug) {
                echo "<pre>Race factor calculation iteration " . ($iteration + 1) . " / " . RACE_RATINGS_NUM_ITERATIONS . "</pre>";
            }

            // While overall difference is reducing, continue this iteration.
            $smallestDistanceBetweenAllRaces = PHP_FLOAT_MAX;
            $distanceImproving = true;
            while ($distanceImproving) {
                $distanceBetweenAllRaces = 0;
                /** @var  $ratingsRace RufRatingsRace */
                foreach ($ratingsRaces->values() as $ratingsRace) {
                    $ratingsRaceType = $ratingsRace->getFullRaceType();
                    $iterationStartFactor = $ratingsRace->getFactor();
                    $rangeAdjust = RACE_RATINGS_INCREMENT_MULTIPLIER * $incrementSize;
                    $startFactor = $iterationStartFactor - $rangeAdjust;
                    $endFactor = $iterationStartFactor + $rangeAdjust;
                    $bestFactor = $iterationStartFactor;
                    $smallestDistanceBetweenRaces = PHP_FLOAT_MAX;
                    for ($tmpFactor = $startFactor; $tmpFactor <= $endFactor; $tmpFactor += $incrementSize) {
                        $distanceBetweenRaces = 0;
                        /** @var  $relatedRaceKeys array */
                        $relatedRaceKeys = $relatedRatingRaceMap->get($ratingsRace->getRaceKey());
                        if ($relatedRaceKeys == null) {
                            continue;
                        }
                        foreach ($relatedRaceKeys as $relatedRaceKey) {
                            if ($relatedRaceKey->equals($ratingsRace->getRaceKey())) {
                                continue;
                            }
                            /** @var  $relatedRace  RufRatingsRace */
                            $relatedRace = $ratingsRaces->get($relatedRaceKey);

                            // Check that this is a compatible race type.
                            $relatedRaceType = $relatedRace->getFullRaceType();
                            if (!$this->isCompatibleRaceType($ratingsRaceType, $relatedRaceType)) {
                                continue;
                            }

                            /** @var  $runnersOfOneRace map , value type RufRatingsRunner */
                            $runnersOfOneRace = $ratingsRace->getRunners();
                            foreach ($runnersOfOneRace->pairs() as $ratingsRunnerEntry) {
                                $horseName = $ratingsRunnerEntry->getKey();
                                /** @var  $ratingsRunner RufRatingsRunner */
                                $ratingsRunner = $ratingsRunnerEntry->getValue();

                                /** @var  $relatedRunner RufRatingsRunner */
                                $relatedRunner = $relatedRace->getRunner($horseName);
                                if ($relatedRunner != null) {
                                    $runnerRating = $ratingsRunner->getRating() * $tmpFactor;
                                    $relatedRunnerRating = $relatedRunner->getRating() * $relatedRace->getFactor();
                                    $distanceBetweenRunners = abs($runnerRating - $relatedRunnerRating);
                                    $distanceBetweenRaces += $distanceBetweenRunners;
                                }
                            }
                        }

                        // If this is the best yet then note it.
                        if ($distanceBetweenRaces < $smallestDistanceBetweenRaces) {
                            $smallestDistanceBetweenRaces = $distanceBetweenRaces;
                            $bestFactor = $tmpFactor;
                        }
                    }

                    $ratingsRace->setFactor($bestFactor);
                    if ($smallestDistanceBetweenRaces != PHP_FLOAT_MAX) {
                        $distanceBetweenAllRaces += $smallestDistanceBetweenRaces;
                    }
                }

                // Don't allow this to go on forever. If the improvement is negligeable, then carry on.
                // Not having this check resulted in hugely bloated running times for some periods of HK data (e.g. 2009-04-05).
                // GDS 2009-09-16: This has been profiled for 2009-09-13 in the UK and is observed at 2bn iterations before the profiler lost count. Reduce limit.
//            if (distanceBetweenAllRaces < smallestDistanceBetweenAllRaces && ((smallestDistanceBetweenAllRaces - distanceBetweenAllRaces) > 0.0000000001)) {
                // if the sum of the distance between *all* races improves by less than 1/2000 we can assume that this is close enough. The difference between this
                // limit and letting it run to the previous limit has been observed at 0.00001 in about 1 in 50 ratings; no ranking differences have been observed.
                // The time saving is ~25% runtime when rating the UK for 2009-09-13.
                if ($distanceBetweenAllRaces < $smallestDistanceBetweenAllRaces && (($smallestDistanceBetweenAllRaces - $distanceBetweenAllRaces) > 0.0005)) {
                    if ($debug) {
                        echo "<pre> New smallest distance (incrementSize = " . $incrementSize . "): " . $distanceBetweenAllRaces . "</pre>";
                    }
                    $smallestDistanceBetweenAllRaces = $distanceBetweenAllRaces;
                } else {
                    $distanceImproving = false;
                }
            }

            // Reduce the increment size to tune the factor more finely in future iterations.
            $incrementSize *= RACE_RATINGS_INCREMENT_REDUCTION_FACTOR;
        }
    }


    private function isCompatibleRaceType(?string $thisRaceType, ?string $relatedRaceType)
    {
        //TODO: always return true for now
        return true;
    }
}

?>