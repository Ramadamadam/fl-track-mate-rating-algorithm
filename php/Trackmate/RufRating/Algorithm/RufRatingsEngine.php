<?php


namespace Trackmate\RufRating\Algorithm;


require_once __DIR__ . '/../DataAccess/RufRatingDataAccess.php';

use DateInterval;
use DateTime;
use Trackmate\RufRating\DataAccess\RufRatingDataAccess;
use Trackmate\RufRating\Model\IRunner;


class RufRatingsEngine
{


    /**
     *
     *
     * @param DateInterval $raceDatesInterval such as "5 months" or "100 days"
     */
    public function processRacesForDate(DateTime $raceDate, DateInterval $raceDatesInterval)
    {
        // This will process the x month period up to and including yesterday (if processing ratings for tomorrow).

        $dataAccess = new RufRatingDataAccess();

        $periodEndDate = date_sub($raceDate, DateInterval::createFromDateString("2 days"));
        $periodStartDate = clone $periodEndDate;
        date_sub($periodStartDate, $raceDatesInterval);

        $periodRunners = $dataAccess->getRunnersBetween($periodStartDate, $periodEndDate);
        $periodRaces = IRunner::extractRacesAsSet($periodRunners)->toArray();

        echo "Found " . count($periodRaces) . " Races to process between " . $periodStartDate->format('Y-m-d') . " and " . $periodEndDate->format('Y-m-d') . ".";

        // Get the runner factors and RufRatingsRaces.
//    Map<Long, Double> runnerFactors = new HashMap<Long, Double>(); //Jian: key = runnerId
//    Map<Long, RufRatingsRace> ratingsRaces = new HashMap<Long, RufRatingsRace>();
//    getRunnerFactorsAndRatingsRaces(periodRaces, runnerFactors, ratingsRaces, racingService);
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
}

?>