<?php

namespace Trackmate\RufRatingRewrite\Algorithm;

//NOTE: The code is imported from the legacy java code "RufRatingsEngine.calculateRaceFactors()"
//DO NOT CHANGE ANYTHING unless you really understand the algorithm

/** The number of iterative refinements performed when calculating race factors. */
define("RACE_RATINGS_NUM_ITERATIONS", 5);
/** The initial increment tick when calculating the race ratings. */
define("RACE_RATINGS_START_INCREMENT_SIZE", 0.25);
/** The multiplier for the race factor increment tick for each iteration. */
define("RACE_RATINGS_INCREMENT_REDUCTION_FACTOR", 0.1);
/** The multiplier which when combined with the increment tick dictates the range of permutations of the race factor per generation. */
define("RACE_RATINGS_INCREMENT_MULTIPLIER", 2);


use Ds\Map;
use Trackmate\RufRatingRewrite\Algorithm\RelatedRaceMatrix;
use Trackmate\RufRatingRewrite\Algorithm\RufRating;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;
use function Trackmate\RufRatingRewrite\DataAccess\get_table_records_by_race_key;


/**
 *
 * @param Map<RaceKey, RaceKey[]> $related_race_matrix
 */
function calculate_race_factors_for_all(RufRatingMiddleResult $ruf_rating_middle_result, array $all_race_runners, Map $related_race_matrix): void
{

    //set the values as 1 at first
    $all_race_set = RaceRunner::extractRacesAsSet($all_race_runners);
    foreach ($all_race_set as $race){
        $ruf_rating_middle_result -> putRaceFactor($race -> race_key, 1);
    }

    // var_dump($ruf_rating_middle_result->getRaceFactorMap());

    $incrementSize = RACE_RATINGS_START_INCREMENT_SIZE;
    for ($iteration = 0; $iteration < RACE_RATINGS_NUM_ITERATIONS; $iteration++) {

        echo "Race factor calculation iteration " . ($iteration + 1) . " / " . RACE_RATINGS_NUM_ITERATIONS;


        //original java comment:  While overall difference is reducing, continue this $iteration.
        $smallestDistanceBetweenAllRaces = PHP_FLOAT_MAX; //type is double in the legacy java code
        $distanceImproving = true; //type is boolean in the legacy java code
        while ($distanceImproving) {

            $distanceBetweenAllRaces = 0; //type is double in the legacy java code

            foreach ($ruf_rating_middle_result->getRaceFactorMap()->pairs() as $race_key_and_race_factor) {  //type is RufRatingsRace, name is "ratingsRace" in the legacy java code

                $this_race_key = $race_key_and_race_factor->key;
                $this_race_runners = RaceRunner::filterByRaceKey($all_race_runners, $this_race_key);

                $iterationStartFactor = $race_key_and_race_factor->value;  //type is double in the legacy java code
                $rangeAdjust = RACE_RATINGS_INCREMENT_MULTIPLIER * $incrementSize; //type is double in the legacy java code
                $startFactor = $iterationStartFactor - $rangeAdjust; //type is double in the legacy java code
                $endFactor = $iterationStartFactor + $rangeAdjust; //type is double in the legacy java code
                $bestFactor = $iterationStartFactor; //type is double in the legacy java code
                $smallestDistanceBetweenRaces = PHP_FLOAT_MAX; //type is double in the legacy java code

                echo "<pre>".$this_race_key->track_name. "  ".$iterationStartFactor." ".$rangeAdjust." ".$startFactor." ".$endFactor." ".$bestFactor." ".$smallestDistanceBetweenRaces.' '.$distanceBetweenAllRaces. "</pre>";


                for ($tmpFactor = $startFactor; $tmpFactor <= $endFactor; $tmpFactor += $incrementSize) {
                    // echo "<pre>".$startFactor.' '.$endFactor.' '.$incrementSize.' '.$tmpFactor."</pre>";

                    $distanceBetweenRaces = 0; //type is double in the legacy java code
                    $relatedRaceKeysCol = $related_race_matrix->get($this_race_key); //type is Collection < Long>  in the legacy java code
                    if ($relatedRaceKeysCol == null) {
                        continue;
                    }

                    foreach ($relatedRaceKeysCol as $relatedRaceKey) { //type is Long in the legacy java code

                        if ($relatedRaceKey->equals($this_race_key)) {
                            continue;
                        }

                        foreach ($this_race_runners as $this_race_runner) { //type is Map.Entry< Long, RufRatingsRunner >  in the legacy java code

                            $related_race_runners = RaceRunner::filterByRaceKey($all_race_runners, $relatedRaceKey);
                            $related_race_runner_with_same_horse = current(RaceRunner::filterByHorseName($related_race_runners, $this_race_runner->horse->horse_name));


                            if (!$related_race_runner_with_same_horse) {
                                continue;
                            }


                            $this_race_runner_factor = $ruf_rating_middle_result->getRunnerFactorByRunnerId($this_race_runner->id);
                            $related_race_runner_factor = $ruf_rating_middle_result->getRunnerFactorByRunnerId($related_race_runner_with_same_horse->id);

                            if (!$this_race_runner_factor || !$related_race_runner_factor) {
                                continue;
                            }

                            $runnerRating = $this_race_runner_factor * $tmpFactor; //type is double in the legacy java code
                            $relatedRunnerRating = $related_race_runner_factor * $ruf_rating_middle_result->getRaceFactorByRaceKey($related_race_runner_with_same_horse->race->race_key); //type is double in the legacy java code
                            $distanceBetweenRunners = abs($runnerRating - $relatedRunnerRating); //type is double in the legacy java code
                            $distanceBetweenRaces += $distanceBetweenRunners;
                        }
                    }

                    // If this is the best yet then note it.
                    if ($distanceBetweenRaces < $smallestDistanceBetweenRaces) {
                        $smallestDistanceBetweenRaces = $distanceBetweenRaces;
                        $bestFactor = $tmpFactor;
                    }
                }


                $ruf_rating_middle_result -> putRaceFactor($this_race_key, $bestFactor);
                if ($smallestDistanceBetweenRaces != PHP_FLOAT_MAX) {
                    $distanceBetweenAllRaces += $smallestDistanceBetweenRaces;
                }
            }

            //the following comment is original java comment
            // Don't allow this to go on forever. If the improvement is negligeable, then carry on.
            // Not having this check resulted in hugely bloated running times for some periods of HK data (e.g. 2009-04-05).
            // GDS 2009-09-16: This has been profiled for 2009-09-13 in the UK and is observed at 2bn iterations before the profiler lost count. Reduce limit.
//            if (distanceBetweenAllRaces < smallestDistanceBetweenAllRaces && ((smallestDistanceBetweenAllRaces - distanceBetweenAllRaces) > 0.0000000001)) {
            // if the sum of the distance between *all* races improves by less than 1/2000 we can assume that this is close enough. The difference between this
            // limit and letting it run to the previous limit has been observed at 0.00001 in about 1 in 50 ratings; no ranking differences have been observed.
            // The time saving is ~25% runtime when rating the UK for 2009-09-13.

            if ($distanceBetweenAllRaces < $smallestDistanceBetweenAllRaces && (($smallestDistanceBetweenAllRaces - $distanceBetweenAllRaces) > 0.0005)) {
                echo "New smallest distance (incrementSize = " . $incrementSize . "): " . $distanceBetweenAllRaces;
                $smallestDistanceBetweenAllRaces = $distanceBetweenAllRaces;
            } else {
                $distanceImproving = false;
            }
        }

        // Reduce the increment size to tune the factor more finely in future iterations.
        $incrementSize *= RACE_RATINGS_INCREMENT_REDUCTION_FACTOR;

        echo "<hr/>";
    }

}

?>