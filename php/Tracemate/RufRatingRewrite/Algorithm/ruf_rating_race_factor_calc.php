<?php

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


use Trackmate\RufRatingRewrite\Model\Race;


/**
 * @param array $related_race_runners values will be changed
 */
function calculate_race_factors_for_all(array $related_race_runners): void
{



    $incrementSize = RACE_RATINGS_START_INCREMENT_SIZE;
    for ($iteration = 0; $iteration < RACE_RATINGS_NUM_ITERATIONS; $iteration++) {

        // While overall difference is reducing, continue this $iteration.
        $smallestDistanceBetweenAllRaces = PHP_FLOAT_MAX; //type is double in the legacy java code
        $distanceImproving = true; //type is xxx in boolean legacy java code
        while ($distanceImproving) {
            $distanceBetweenAllRaces = 0; //type is double in the legacy java code
            for ($ratingsRace : ratingsRaces . values()) {  //type is RufRatingsRace in the legacy java code

                $iterationStartFactor = $ratingsRace . getFactor();  //type is double in the legacy java code
                $rangeAdjust = RACE_RATINGS_INCREMENT_MULTIPLIER * $incrementSize; //type is double in the legacy java code
                $startFactor = $iterationStartFactor - $rangeAdjust; //type is double in the legacy java code
                $endFactor = $iterationStartFactor + $rangeAdjust; //type is double in the legacy java code
                $bestFactor = $iterationStartFactor; //type is double in the legacy java code
                $smallestDistanceBetweenRaces = Double . MAX_VALUE; //type is double in the legacy java code
                for ($tmpFactor = $startFactor; $tmpFactor <= $endFactor; $tmpFactor += $incrementSize) {
                    $distanceBetweenRaces = 0; //type is double in the legacy java code
                    $relatedRaceIdsCol = $relatedRaceIds . get($ratingsRace . getRaceId()); //type is Collection < Long>  in the legacy java code
                    if ($relatedRaceIdsCol == null) {
                        continue;
                    }
                    for ($relatedRaceId : $relatedRaceIdsCol) { //type is Long in the legacy java code
                        if ($relatedRaceId . equals($ratingsRace . getRaceId())) {
                            continue;
                        }
                        RufRatingsRace $relatedRace = ratingsRaces . get($relatedRaceId); //type is xxx in the legacy java code



              for ($ratingsRunnerEntry : $ratingsRace . getRunners() . entrySet()) { //type is Map.Entry< Long, RufRatingsRunner >  in the legacy java code
                  $relatedRunner = $relatedRace . getRunner($ratingsRunnerEntry . getKey()); //type is RufRatingsRunner in the legacy java code
                  if ($relatedRunner != null) {
                      $runnerRating = $ratingsRunnerEntry . getValue() . getRating() * $tmpFactor; //type is double in the legacy java code
                      $relatedRunnerRating = $relatedRunner . getRating() * $relatedRace . getFactor(); //type is double in the legacy java code
                      $distanceBetweenRunners = Math . abs($runnerRating - $relatedRunnerRating); //type is double in the legacy java code
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

          $ratingsRace . setFactor($bestFactor);
          if ($smallestDistanceBetweenRaces != Double . MAX_VALUE) {
              $distanceBetweenAllRaces += $smallestDistanceBetweenRaces;
          }
        }

 
        if ($distanceBetweenAllRaces < $smallestDistanceBetweenAllRaces && (($smallestDistanceBetweenAllRaces - $distanceBetweenAllRaces) > 0.0005)) {

            $smallestDistanceBetweenAllRaces = $distanceBetweenAllRaces;
        } else {
            $distanceImproving = false;
        }
      }

      // Reduce the increment size to tune the factor more finely in future iterations.
      $incrementSize *= RACE_RATINGS_INCREMENT_REDUCTION_FACTOR;
    }
}

}

?>