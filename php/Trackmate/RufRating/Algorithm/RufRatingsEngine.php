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
use Trackmate\RufRating\Algorithm\RufRatingsRace;
use Trackmate\RufRating\Algorithm\RufRatingsRunner;


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

        $this -> getRunnerFactorsAndRatingsRaces($periodRaces, $periodRunners, $runnerFactors, $ratingsRaces);

        if($debug){
            echo "<p>The runner factors are: </p>";
            echo "<pre>";
            var_dump($runnerFactors);
            echo "</pre>";
        }
//
//    //////
//    // Build up all related races.
//    //////
//    Map<Long, Collection<Long>> relatedRaceIds = new HashMap<Long, Collection<Long>>();
//    Map<Long, Collection<RufRatingsRace>> horseRaceRatings = new HashMap<Long, Collection<RufRatingsRace>>();
//    getRelatedRaces(ratingsRaces, relatedRaceIds, horseRaceRatings);
//
//    //////
//    // Calculate the RaceFactors
//    //////
//    if (debug) {
//        logger.debug("Calculating race factors for " + ratingsRaces.size() + " Races.");
//    }
//    calculateRaceFactors(ratingsRaces, relatedRaceIds, debug);
//    logger.info("Race factor calculation complete (100%)");
//
//
//    if (debug) {
//        logger.debug("Calculating final ratings for " + racesForDate.size() + " Races (Meeting " + marketId + ", Date: " +
//            raceDate + ").");
//    }
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
                $ratingsRaces ->put($ratingsRace->getRaceKey(), $ratingsRace);
            }
        }
    }


}

?>