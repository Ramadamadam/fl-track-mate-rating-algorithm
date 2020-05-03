/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT
 * OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
 * TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Copyright (c) 2000-2010 TrackMate.
 */
package info.track_mate.racing.ratings.ruf;

import info.track_mate.racing.RacingConfig;
import info.track_mate.racing.RacingHelper;
import info.track_mate.racing.domain.IHorse;
import info.track_mate.racing.domain.IMarket;
import info.track_mate.racing.domain.IRace;
import info.track_mate.racing.domain.IRunner;
import info.track_mate.racing.domain.RunnerValue;
import info.track_mate.racing.ratings.AbstractRatingsEngine;
import info.track_mate.racing.ratings.RatingsGenerationStatus;
import info.track_mate.racing.ratings.RatingsHelper;
import info.track_mate.racing.ratings.RatingsHelper.FullRaceType;
import info.track_mate.racing.ratings.RatingsRaceValidityChecker;
import info.track_mate.racing.ratings.RatingsRankingCalculator;
import info.track_mate.racing.ratings.domain.IRatingsType;
import info.track_mate.racing.ratings.domain.IRatingsValueSet;
import info.track_mate.racing.ratings.domain.RatingsTypeValue;
import info.track_mate.racing.ratings.domain.RatingsValueSetValue;
import info.track_mate.racing.ratings.service.RatingsServiceRemote;
import info.track_mate.racing.service.RacingServiceRemote;
import info.track_mate.util.DefaultPageByPageDefinition;
import info.track_mate.util.DefaultValueObjectDefinition;
import info.track_mate.util.PageByPageDefinition;
import info.track_mate.util.PageFilter;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collection;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

/**
 * The logic implmentation of the Ruf ratings algorithm.
 *
 * @author Gareth Smith
 */
public class RufRatingsEngine extends AbstractRatingsEngine {

  /** The name of the RatingsType containing the rating. */
  public static final String RATINGS_NAME_RUNNER_RATING = "RufRating";
  /** The name of the RatingsType containing the best value for the stack. */
  public static final String RATINGS_NAME_STACK_BEST = "RufStackBest";
  /** The name of the RatingsType containing the average value for the stack. */
  public static final String RATINGS_NAME_STACK_AVERAGE = "RufStackAverage";
  /** The name of the RatingsType containing the worst value for the stack. */
  public static final String RATINGS_NAME_STACK_WORST = "RufStackWorst";
  /** The name of the RatingsType containing the last value for the stack. */
  public static final String RATINGS_NAME_STACK_LAST = "RufStackLast";
  /** The name of the RatingsType containing the trend. */
  public static final String RATINGS_NAME_TREND = "RufTrendPoint";

  /** Logger instance for this class. */
  private static org.apache.log4j.Logger logger = org.apache.log4j.Logger.getLogger(RufRatingsEngine.class);

  /** The number of iterative refinements performed when calculating race factors. */
  private static final int RACE_RATINGS_NUM_ITERATIONS = 5;
  /** The initial increment tick when calculating the race ratings. */
  private static final double RACE_RATINGS_START_INCREMENT_SIZE = 0.25;
  /** The multiplier for the race factor increment tick for each iteration. */
  private static final double RACE_RATINGS_INCREMENT_REDUCTION_FACTOR = 0.1;
  /** The multiplier which when combined with the increment tick dictates the range of permutations of the race factor per generation. */
  private static final int RACE_RATINGS_INCREMENT_MULTIPLIER = 2;

  /** The index used to store the processed Dates in the status Map. */
  private static final int STATUS_MAP_ALL_DATES = 1;
  /** The index used to store the unsuccessful Dates in the status Map. */
  private static final int STATUS_MAP_UNSUCCESSFUL_DATES = 2;

  @Override
  protected RatingsGenerationStatus intGenerateRatings(RacingServiceRemote racingService, RatingsServiceRemote ratingsService, Collection<Long> ratingsTypeIds,
          Collection<Long> raceIds, int ratingsCalculationType) throws Exception {
    logger.info("Processing Ruf Ratings");
    Collection<IRace> races = racingService.getRaces(raceIds, DefaultValueObjectDefinition.VODEF_ALL_ATTRIBUTES);
    int failures = 0;
    Map<Long, Set<Date>> allDates = Collections.EMPTY_MAP;
    Map<Long, Set<Date>> unsuccessfulDates = Collections.EMPTY_MAP;
    for (Long ratingsTypeId : ratingsTypeIds) {
      if (logger.isDebugEnabled()) {
        logger.debug("Processing Ruf ratings for RatingsType " + ratingsTypeId + " on Races: " + races);
      }
      try {
        Map<Integer, Map<Long, Set<Date>>> processStatus = processRatingsType(ratingsTypeId, races, racingService, ratingsService);
        allDates = processStatus.get(STATUS_MAP_ALL_DATES);
        unsuccessfulDates = processStatus.get(STATUS_MAP_UNSUCCESSFUL_DATES);

      } catch (Exception e) {
        failures++;
        logger.error("Failed to process Ruf ratings type: " + ratingsTypeId, e);
      }
      if (logger.isDebugEnabled()) {
        logger.debug("Finished processing Ruf ratings for RatingsType: " + ratingsTypeId);
      }
    }
    if (logger.isInfoEnabled()) {
      logger.info("Processed all " + ratingsTypeIds.size() + " RatingsTypes for Ruf ratings engine.");
    }

    // Calculate the ranks of the ratings.
    if (failures < ratingsTypeIds.size()) {
      rankRatings(raceIds, ratingsTypeIds, racingService, ratingsService);
    }

    // Put together the status of the operation.
    RatingsGenerationStatus generationStatus = new RatingsGenerationStatus();
    for (Long marketId : allDates.keySet()) {
      Set<Date> marketRaceDates = allDates.get(marketId);
      Set<Date> marketUnsuccessfulRaceDates = unsuccessfulDates.get(marketId);
      for (Date raceDate : marketRaceDates) {
        if (marketUnsuccessfulRaceDates.contains(raceDate)) {
          generationStatus.addUnsuccessfulDate(marketId, raceDate);
        } else {
          generationStatus.addSuccessfulDate(marketId, raceDate);
        }
      }
    }
    return generationStatus;
  }

  @Override
  public void rankRatings(Collection<Long> raceIds, Collection<Long> ratingsTypeIds, RacingServiceRemote racingService, RatingsServiceRemote ratingsService)
    throws Exception {
    try {
      RatingsRankingCalculator ratingsRankingCalculator = new RufRatingsRankingCalculator();
      ratingsRankingCalculator.process(ratingsService, racingService, raceIds, ratingsTypeIds);
    } catch (Exception e) {
      logger.error("Failed to calculate ranks for Ruf ratings", e);
    }
  }

  // ------ Private Helpers ------

  /**
   * Process the ratings for the specified ratings type in the specified races.
   * @param ratingsTypeId The ID of the RatingsType to generate the ratings for.
   * @param races The Races to generate the ratings for.
   * @param racingService the RacingService.
   * @param ratingsService The RatingsService.
   * @return The collection of ratings for the runners in the specified races.
   * @throws Exception If the ratings could not be generated.
   */
  private Map<Integer, Map<Long, Set<Date>>> processRatingsType(long ratingsTypeId, Collection<IRace> races, RacingServiceRemote racingService,
          RatingsServiceRemote ratingsService) throws Exception {
    if (races == null || races.isEmpty()) {
      if (logger.isDebugEnabled()) {
        logger.debug("No races to process, ignoring request.");
      }
      return Collections.EMPTY_MAP;
    }

    boolean debug = logger.isDebugEnabled();

    // Create a validity checker to decide which races to use in the calculation.
    RatingsRaceValidityChecker ratingsRaceValidityChecker = new RatingsRaceValidityChecker(ratingsTypeId, ratingsService);

    //////
    // Get all Races & Runners for the period and calculate the RunnerFactors.
    //////

    // Get all races for the last 6 months.
    // Get the RatingsRaceValidityChecker to tell us the period we need to retrieve.
    Integer calendarModificationUnit = ratingsRaceValidityChecker.getCalendarModifactionUnit();
    Integer calendarModifactionQuantity = ratingsRaceValidityChecker.getCalendarModifactionQuantity();
    // TODO: for 'last x races' style ratings types get 6 months of data.
    if (calendarModificationUnit == null || calendarModifactionQuantity == null) {
      calendarModificationUnit = Calendar.MONTH;
      calendarModifactionQuantity = 6;
    }

    Map<Long, Map<Date, Collection<IRace>>> racesByDateAndMarket = collateRacesByMarketAndDate(races, racingService);
    int numRacesToProcess = races.size();
    int numRacesProcessed = 0;
    Map<Long, Set<Date>> unsuccessfulDates = new HashMap<Long, Set<Date>>();
    Map<Long, Set<Date>> allDates = new HashMap<Long, Set<Date>>();
    for (Map.Entry<Long, Map<Date, Collection<IRace>>> racesByDateForMarket : racesByDateAndMarket.entrySet()) {
      Long marketId = racesByDateForMarket.getKey();

      Set<Date> marketUnsuccessfulDates = new HashSet<Date>();
      unsuccessfulDates.put(marketId, marketUnsuccessfulDates);
      Set<Date> marketAllDates = new HashSet<Date>();
      allDates.put(marketId, marketAllDates);
      for (Map.Entry<Date, Collection<IRace>> racesForDate : racesByDateForMarket.getValue().entrySet()) {
        Date raceDate = racesForDate.getKey();

        marketAllDates.add(raceDate);
        // This will process the x month period up to and including yesterday (if processing ratings for tomorrow).
        Date periodEndDate = RacingHelper.getOffsetDate(raceDate, -2, Calendar.DATE);
        Date periodStartDate = RacingHelper.getOffsetDate(periodEndDate, -calendarModifactionQuantity, calendarModificationUnit);

        Collection<IRace> periodRaces = RatingsHelper.getRacesByMarketAndPeriod(marketId, periodStartDate, periodEndDate,
                DefaultValueObjectDefinition.createValueObjectDefinition("*,raceType.*,track.*"), racingService);
        if (debug) {
          logger.debug("Found " + periodRaces.size() + " Races to process between " + periodStartDate + " and " + periodEndDate + ".");
        }
        // Get the runner factors and RufRatingsRaces.
        Map<Long, Double> runnerFactors = new HashMap<Long, Double>(); //Jian: key = runnerId
        Map<Long, RufRatingsRace> ratingsRaces = new HashMap<Long, RufRatingsRace>();
        getRunnerFactorsAndRatingsRaces(periodRaces, runnerFactors, ratingsRaces, racingService);

        //////
        // Build up all related races.
        //////
        Map<Long, Collection<Long>> relatedRaceIds = new HashMap<Long, Collection<Long>>();
        Map<Long, Collection<RufRatingsRace>> horseRaceRatings = new HashMap<Long, Collection<RufRatingsRace>>();
        getRelatedRaces(ratingsRaces, relatedRaceIds, horseRaceRatings);

        //////
        // Calculate the RaceFactors
        //////
        if (debug) {
          logger.debug("Calculating race factors for " + ratingsRaces.size() + " Races.");
        }
        calculateRaceFactors(ratingsRaces, relatedRaceIds, debug);
        logger.info("Race factor calculation complete (100%)");

        if (debug) {
          logger.debug("Calculating final ratings for " + racesForDate.getValue().size() + " Races (Meeting " + marketId + ", Date: " +
                  raceDate + ").");
        }

        //////
        // Store the ratings.
        //////
        try {
          storeRatings(racesForDate.getValue(), ratingsTypeId, ratingsRaceValidityChecker, horseRaceRatings, ratingsRaces, racingService, ratingsService,
                  debug);
        } catch (Exception e) {
          logger.error("Failed to store ratings for date: " + raceDate, e);
          marketUnsuccessfulDates.add(raceDate);
          continue;
        }
        // Output some progress info.
        if (logger.isInfoEnabled()) {
          numRacesProcessed++;
          int infoFrequency = numRacesToProcess / 4;
          int frequencyModulus = numRacesProcessed % infoFrequency;
          if (frequencyModulus == 0) {
            logger.info("Processed " + numRacesProcessed + " / " + numRacesToProcess + " (" +
                (int)(((double)numRacesProcessed / (double)numRacesToProcess)*100) + "%) races for ratings type: " + ratingsTypeId);
          }
        }
      }
    }
    logger.info("Processed all " + races.size() + " races for Ruf ratings type: " + ratingsTypeId);
    Map<Integer, Map<Long, Set<Date>>> toReturn = new HashMap<Integer, Map<Long, Set<Date>>>();
    toReturn.put(STATUS_MAP_ALL_DATES, allDates);
    toReturn.put(STATUS_MAP_UNSUCCESSFUL_DATES, unsuccessfulDates);
    return toReturn;
  }

  /**
   * Return a Map of the supplied Races, keyed by race date.
   * @param unsortedRaces The RaceVOs to index.
   * @param racingService The RacingService.
   * @return The Races indexed by race date.
   */
  private Map<Long, Map<Date, Collection<IRace>>> collateRacesByMarketAndDate(Collection<IRace> unsortedRaces, RacingServiceRemote racingService) {
    Map<Long, Map<Date, Collection<IRace>>> toReturn = new HashMap<Long, Map<Date, Collection<IRace>>>();
    for (IRace race : unsortedRaces) {
      // Map by Market.
      IMarket market = racingService.getMarketForRace(race.getId(), DefaultValueObjectDefinition.VODEF_ID_ONLY);
      Map<Date, Collection<IRace>> racesByDateForMarket = toReturn.get(market.getId());
      if (racesByDateForMarket == null) {
        racesByDateForMarket = new HashMap<Date, Collection<IRace>>();
        toReturn.put(market.getId(), racesByDateForMarket);
      }

      // Collection by Date.
      Date raceDate = race.getRaceDate();
      Collection<IRace> racesForDate = racesByDateForMarket.get(raceDate);
      if (racesForDate == null) {
        racesForDate = new ArrayList<IRace>();
        racesByDateForMarket.put(raceDate, racesForDate);
      }
      racesForDate.add(race);
    }
    return toReturn;
  }

  /**
   * Get the runner factors and RufRatingsRaces for the supplied Races.
   * @param periodRaces The collection of Races to iterate over to get the runner factors and RufRatingsRaces from.
   * @param runnerFactors The map to insert the runner factors into (keyed on runner ID).
   * @param ratingsRaces The map to insert the RufRatingsRaces into (keyed on race ID).
   * @param racingService The RacingService.
   * @throws Exception On error.
   */
  private void getRunnerFactorsAndRatingsRaces(Collection<IRace> periodRaces, Map<Long, Double> runnerFactors, Map<Long, RufRatingsRace> ratingsRaces,
      RacingServiceRemote racingService) throws Exception {
    for (IRace periodRace : periodRaces) {
      // Build up a RufRatingsRace.
      RufRatingsRace ratingsRace = new RufRatingsRace(periodRace.getId());
      FullRaceType fullRaceType = FullRaceType.getFullRaceType(periodRace);
      ratingsRace.setFullRaceType(fullRaceType);

      double raceDistance = periodRace.getDistance();

      // Get all runners for the race.
      Collection<IRunner> raceRunners = racingService.getRunners(periodRace.getId(), DefaultValueObjectDefinition.createValueObjectDefinition("*,horse.*"));
      for (IRunner raceRunner : raceRunners) {
        // Ignore non-runners.
        if (raceRunner.getStatus() == RacingConfig.RunnerStatus.NON_RUNNER) {
          continue;
        }
        // Ignore runners with no result.
        Double distanceFromWinner = raceRunner.getDistanceFromWinner();
        Integer position = raceRunner.getPosition();
        if (distanceFromWinner == null || position == null || !raceRunner.getPositionStr().matches("\\d+")) {
          continue;
        }
        // Build up a RufRatingsRunner.
        double runnerFactor = raceDistance /
                (raceDistance - distanceFromWinner);
        runnerFactors.put(raceRunner.getId(), runnerFactor);

        if (runnerFactor >= 1.02) {
          // If the runner was too far behind then don't rate.
          if (logger.isDebugEnabled()) {
           logger.debug("Runner has ruf factor of >= 1.02 (" + runnerFactor + "): " + raceRunner.getId() + " (" + raceRunner.getHorse().getName() + ")");
          }
          //continue;
        } else {
          RufRatingsRunner ratingsRunner = new RufRatingsRunner();
          ratingsRunner.setDate(periodRace.getRaceDate());
          ratingsRunner.setHorseId(raceRunner.getHorse().getId());
          ratingsRunner.setRating(runnerFactor);
          ratingsRace.addRunner(ratingsRunner);
        }
      }

      if (ratingsRace.getRunners().size() > 0) {
        ratingsRaces.put(ratingsRace.getRaceId(), ratingsRace);
      }
    }
  }

  /**
   *  Build up the map of related races for each race. Also build up the map of races each horse was involved in.
   * @param ratingsRaces The map of RufRatingsRaces, keyed on race ID.
   * @param relatedRaceIds The map of Collections of RufRatingsRaces, keyed on race ID.   Jian: output parameter
   * @param horseRaceRatings The map of Collections of RufRatingsRaces, keyed on horse ID. Jian: output parameter
   * @throws Exception If the related races Map couldn't be built.
   */
  private void getRelatedRaces(Map<Long, RufRatingsRace> ratingsRaces, Map<Long, Collection<Long>> relatedRaceIds,
          Map<Long, Collection<RufRatingsRace>> horseRaceRatings) throws Exception {

    // Horse.id -> {Race.id}
    Map<Long, Collection<Long>> horseAndTheirRaces = new HashMap<Long, Collection<Long>>(); //Jian: horse and the races they have been really in
    // Get a map of all races each horse has been in.
    for (RufRatingsRace ratingsRace : ratingsRaces.values()) {
      Map<Long, RufRatingsRunner> runners = ratingsRace.getRunners();
      for (Long horseId : runners.keySet()) {
        Collection<Long> horseRaces = horseAndTheirRaces.get(horseId);
        if (horseRaces == null) {
          horseRaces = new ArrayList<Long>();
          horseAndTheirRaces.put(horseId, horseRaces);
        }
        horseRaces.add(ratingsRace.getRaceId());
      }
    }

    for (RufRatingsRace ratingsRace : ratingsRaces.values()) {
      Collection<Long> relatedRaces = relatedRaceIds.get(ratingsRace.getRaceId());  //Jian: initialise the the map of relatedRaceIds - start
      if (relatedRaces == null) {
        relatedRaces = new ArrayList<Long>();
        relatedRaceIds.put(ratingsRace.getRaceId(), relatedRaces);
      } //Jian: initialise the the map of relatedRaceIds - end
      for (RufRatingsRunner ratingsRunner : ratingsRace.getRunners().values()) { //Jian: for each race, loop thru its runners
        Collection<Long> horseRaceIds = horseAndTheirRaces.get(ratingsRunner.getHorseId()); //Jian: get all races this horse has been in
        for (Long horseRaceId : horseRaceIds) {
          if (!horseRaceId.equals(ratingsRace.getRaceId()) //Jian:  not the same race
                  && !relatedRaces.contains(horseRaceId)) { //Jian: the race hasn't been included to result map yet
            // If the 2 races are compatible then make a note of the relationship.
            FullRaceType thisRaceType = ratingsRace.getFullRaceType();
            FullRaceType relatedRaceType = ratingsRaces.get(horseRaceId).getFullRaceType();
            if (RatingsHelper.isCompatibleRaceType(thisRaceType, relatedRaceType)) {
              relatedRaces.add(horseRaceId);
            }
          }
        }
      }

      // If this race has related races, then make a note that all the horses ran in this race.
      if (!relatedRaces.isEmpty()) {
        for (RufRatingsRunner ratingsRunner : ratingsRace.getRunners().values()) {
          Collection<RufRatingsRace> horseRaces = horseRaceRatings.get(ratingsRunner.getHorseId());
          if (horseRaces == null) {
            horseRaces = new ArrayList<RufRatingsRace>();
            horseRaceRatings.put(ratingsRunner.getHorseId(), horseRaces);
          }
          horseRaces.add(ratingsRace);
        }
      }
    }
  }

  /**
   * Calculate and store the ratings for the runners.
   * @param targetRaces The Collection of Races we're rating.
   * @param ratingsTypeId The ID of the RatingsType.
   * @param ratingsRaceValidityChecker The relevant RatingsRaceValidityChecker.
   * @param horseRaceRatings The Map of Collections of RufRatingsRace keyed on Horse ID.
   * @param ratingsRaces The Map of RudRatingsRace keyed on Race ID.
   * @param racingService The RacingService.
   * @param ratingsService The RatingsService.
   * @param debug A boolean flag equivalent to logger.isDebugEnabled() to reduce calls to the logger variable (possibly futile, don't know).
   * @throws Exception On error.
   */
  private void storeRatings(Collection<IRace> targetRaces, Long ratingsTypeId, RatingsRaceValidityChecker ratingsRaceValidityChecker,
      Map<Long, Collection<RufRatingsRace>> horseRaceRatings, Map<Long, RufRatingsRace> ratingsRaces, RacingServiceRemote racingService,
      RatingsServiceRemote ratingsService, boolean debug) throws Exception {

    // Process each specified Race.
    for (IRace targetRace : targetRaces) {
      if (logger.isDebugEnabled()) {
        logger.debug("Processing Ruf ratings (" + ratingsTypeId + ") for race: " + targetRace.getId() + " (" + targetRace.getStartTime() + ")");
      }
      // Set the target race for the validity checking.
      ratingsRaceValidityChecker.setTargetRace(targetRace);

      // Process each Runner in the Race.
      Collection<IRunner> runners = racingService.getRunners(targetRace.getId(), DefaultValueObjectDefinition.createValueObjectDefinition("*,horse.*,race.*," +
              "race.raceType.*,race.track.*"));
      for (IRunner targetRunner : runners) {
        if (debug) {
          logger.debug("  Processing '" + ratingsRaceValidityChecker.getRatingsPeriodString() + "' Ruf ratings for runner: " +
            targetRunner.getHorse().getName() + " (" + targetRunner.getId() + ")");
        }

        // Process all runs of the horse within the timeframe.
        IHorse horse = targetRunner.getHorse();
        Long horseId = horse.getId();
        Collection<IRunner> horseRunners;
        try {
          PageByPageDefinition pbpDef = new DefaultPageByPageDefinition();
          pbpDef.setValueObjectDefinition(DefaultValueObjectDefinition.createValueObjectDefinition("*,race.*,race.raceType.*,race.track.*"));
          pbpDef.addFilter(new PageFilter("horse.id", PageFilter.ParameterOperator.EQUALS, horseId, PageFilter.FilterOperator.AND));
          horseRunners = racingService.list(IRunner.class, pbpDef);
        } catch (Exception e) {
          throw new Exception("Failed to get runners for horse: " + horseId, e);
        }
        // Set the collection of Runners for this horse on the ratings period validity helper.
        ratingsRaceValidityChecker.setHorseRunners(horseRunners);

        Collection<RufRatingsRace> horseRatingsRaces = horseRaceRatings.get(horseId);
        int numRuns = 0;
        double totalRating = 0;
        Double bestRating = null;
        Double worstRating = null;
        Double lastRating = null;
        Date lastRatingDate = null;
        if (horseRatingsRaces != null) {
          // Check every runner for this horse.
          for (IRunner thisRunner : horseRunners) {
            Long thisRunnerId = thisRunner.getId();

            // Ignore non-runners.
            if (thisRunner.getStatus() == RacingConfig.RunnerStatus.NON_RUNNER) {
              continue;
            }

            // Ignore the runner if it is outside the scope of this calculation.
            IRace thisRace = thisRunner.getRace();
            if (debug) {
              logger.debug("    Found runner. date: " + thisRace.getStartTime() + " ID: " + thisRunnerId);
            }

            Date thisRaceDate = thisRace.getRaceDate();
            if (!ratingsRaceValidityChecker.isRaceWithinRatingsPeriod(thisRaceDate, thisRunner.getId())) {
              continue;
            }

            FullRaceType currentRaceType = FullRaceType.getFullRaceType(targetRunner.getRace());
            FullRaceType previousRaceType = FullRaceType.getFullRaceType(thisRunner.getRace());
            boolean isRaceCompatible = RatingsHelper.isCompatibleRaceType(currentRaceType, previousRaceType);
            if (!isRaceCompatible) {
              if (debug) {
                logger.debug("    Past race type (" + previousRaceType + ") is incompatible with current race type: " + currentRaceType);
              }
              continue;
            }

            RufRatingsRace ratingsRace = ratingsRaces.get(thisRace.getId());
            if (ratingsRace == null) {
              continue;
            }

            RufRatingsRunner ratingsRunner = ratingsRace.getRunner(horseId);
            if (ratingsRunner != null) {
              double raceFactor = ratingsRace.getFactor();
              double runnerFactor = ratingsRunner.getRating();
              double runnerRating = runnerFactor * raceFactor;
              totalRating += runnerRating;
              if (debug) {
                logger.debug("  Related runner for horse: " + horseId);
                logger.debug("    RunnerFactor = " + runnerFactor);
                logger.debug("    RaceFactor = " + raceFactor);
                logger.debug("    RunnerRating = " + runnerRating);
              }
              numRuns++;

              // Values for the stack.
              if (bestRating == null || runnerRating < bestRating) {
                bestRating = runnerRating;
              }
              if (worstRating == null || runnerRating > worstRating) {
                worstRating = runnerRating;
              }
              if (lastRating == null || thisRaceDate.after(lastRatingDate)) {
                lastRating = runnerRating;
                lastRatingDate = thisRaceDate;
              }

              // Store the runner factor as the value for the trend point as this will not change over time.
  //                Map<String, Double> runnerRatings = new HashMap<String, Double>();
  //            runnerRatings.put(RATINGS_NAME_TREND, runnerFactor);
              // TODO: We CANNOT do this. The race factor changes each time it is calculated anew, meaning that we can change the historic view of the race
              // by doing this.
  //                runnerRatings.put(RATINGS_NAME_TREND, runnerRating);
  //                ratingsService.storeRatings(runnerRatings, ratingsTypeId, thisRunnerId);

  //            RatingsValueSetVO ratingsValueSet = (RatingsValueSetVO)ratingsService.findBy("RatingsValueSet", "RunnerAndRatingsType",
  //                RatingsValueSetDao.TRANSFORM_RATINGSVALUESETVO, new Long[]{thisRunnerId, ratingsTypeId});
  //            if (ratingsValueSet == null) {
  //              ratingsValueSet = new RatingsValueSetVO();
  //            }
  //            ratingsValueSet.setTrendPoint(runnerRating);
  //            ratingsService.storeRatings(ratingsValueSet, null, ratingsTypeId, thisRunnerId);
              PageByPageDefinition pbpDef = new DefaultPageByPageDefinition();
              pbpDef.setValueObjectDefinition(DefaultValueObjectDefinition.VODEF_ID_ONLY);
              pbpDef.addFilter(new PageFilter("runner.id", PageFilter.ParameterOperator.EQUALS, thisRunnerId, PageFilter.FilterOperator.AND));
              pbpDef.addFilter(new PageFilter("ratingsType.id", PageFilter.ParameterOperator.EQUALS, ratingsTypeId, PageFilter.FilterOperator.AND));
              Collection<IRatingsValueSet> ratingsValueSets = ratingsService.list(IRatingsValueSet.class, pbpDef);
              if (ratingsValueSets.size() > 1) {
                throw new Exception("Found multiple RatingsValueSets for Runner '" + thisRunnerId + "' and RatingsType '" + ratingsTypeId +
                        "', expected at most 1: " + ratingsValueSets);
              }
              IRatingsValueSet ratingsValueSet;
              if (ratingsValueSets.size() == 1) {
                ratingsValueSet = ratingsValueSets.iterator().next();
              } else {
                ratingsValueSet = new RatingsValueSetValue();
                IRunner dummyRunner = new RunnerValue();
                dummyRunner.setId(thisRunnerId);
                IRatingsType dummyRatingsType = new RatingsTypeValue();
                dummyRatingsType.setId(ratingsTypeId);
                ratingsValueSet.setRunner(dummyRunner);
                ratingsValueSet.setRatingsType(dummyRatingsType);
              }
              ratingsValueSet.setTrendPoint(runnerRating);
              ratingsService.createOrUpdateEntity(IRatingsValueSet.class, ratingsValueSet, null);
            }
          }
        }

        // Store the rating.
        Double averageRating = null;
        if (numRuns > 0) {
          averageRating = totalRating / numRuns;
        }
        if (debug) {
          logger.debug("  Runner " + targetRunner.getHorse().getName());
          logger.debug("    NumRuns = " + numRuns);
          logger.debug("    FinalRating = " + averageRating);
          logger.debug("    StackBest = " + bestRating);
          logger.debug("    StackAverage = " + averageRating);
          logger.debug("    StackWorst = " + worstRating);
          logger.debug("    StackLast = " + lastRating);
        }

        IRatingsValueSet ratingsSet = new RatingsValueSetValue();
        ratingsSet.setRating(averageRating);
        ratingsSet.setStackBest(bestRating);
        ratingsSet.setStackAverage(averageRating);
        ratingsSet.setStackWorst(worstRating);
        ratingsSet.setStackLast(lastRating);
        ratingsService.storeRatings(ratingsSet, null, ratingsTypeId, targetRunner.getId());
      }
    }
  }

  /**
   * Calculate the Race factors for each Race in the period.
   * @param ratingsRaces The Map of RudRatingsRace, keyed on Race ID.
   * @param relatedRaceIds The Map of Collections of related Race IDs, keyed on Race ID.
   * @param debug A boolean flag equivalent to logger.isDebugEnabled() to reduce calls to the logger variable (possibly futile, don't know).
   * @throws Exception on error.
   */
  private void calculateRaceFactors(Map<Long, RufRatingsRace> ratingsRaces, Map<Long, Collection<Long>> relatedRaceIds, boolean debug) throws Exception {
    double incrementSize = RACE_RATINGS_START_INCREMENT_SIZE;
    for (int iteration = 0; iteration < RACE_RATINGS_NUM_ITERATIONS; iteration++) {
      if (logger.isInfoEnabled()) {
        logger.info("Race factor calculation iteration " + (iteration + 1) + " / " + RACE_RATINGS_NUM_ITERATIONS + "(" +
            (int)(((double)iteration / (double)RACE_RATINGS_NUM_ITERATIONS)*100) + "%)");
      }

      // While overall difference is reducing, continue this iteration.
      double smallestDistanceBetweenAllRaces = Double.MAX_VALUE;
      boolean distanceImproving = true;
      while (distanceImproving) {
        double distanceBetweenAllRaces = 0;
        for (RufRatingsRace ratingsRace : ratingsRaces.values()) {
          FullRaceType ratingsRaceType = ratingsRace.getFullRaceType();
          double iterationStartFactor = ratingsRace.getFactor();
          double rangeAdjust = RACE_RATINGS_INCREMENT_MULTIPLIER * incrementSize;
          double startFactor = iterationStartFactor - rangeAdjust;
          double endFactor = iterationStartFactor + rangeAdjust;
          double bestFactor = iterationStartFactor;
          double smallestDistanceBetweenRaces = Double.MAX_VALUE;
          for (double tmpFactor = startFactor; tmpFactor <= endFactor; tmpFactor += incrementSize) {
            double distanceBetweenRaces = 0;
            Collection<Long> relatedRaceIdsCol = relatedRaceIds.get(ratingsRace.getRaceId());
            if (relatedRaceIdsCol == null) {
              continue;
            }
            for (Long relatedRaceId : relatedRaceIdsCol) {
              if (relatedRaceId.equals(ratingsRace.getRaceId())) {
                continue;
              }
              RufRatingsRace relatedRace = ratingsRaces.get(relatedRaceId);

              // Check that this is a compatible race type.
              FullRaceType relatedRaceType = relatedRace.getFullRaceType();
              if (!RatingsHelper.isCompatibleRaceType(ratingsRaceType, relatedRaceType)) {
                continue;
              }

              for (Map.Entry<Long, RufRatingsRunner> ratingsRunnerEntry : ratingsRace.getRunners().entrySet()) {
                RufRatingsRunner relatedRunner = relatedRace.getRunner(ratingsRunnerEntry.getKey());
                if (relatedRunner != null) {
                  double runnerRating = ratingsRunnerEntry.getValue().getRating() * tmpFactor;
                  double relatedRunnerRating = relatedRunner.getRating() * relatedRace.getFactor();
                  double distanceBetweenRunners = Math.abs(runnerRating - relatedRunnerRating);
                  distanceBetweenRaces += distanceBetweenRunners;
                }
              }
            }

            // If this is the best yet then note it.
            if (distanceBetweenRaces < smallestDistanceBetweenRaces) {
              smallestDistanceBetweenRaces = distanceBetweenRaces;
              bestFactor = tmpFactor;
            }
          }

          ratingsRace.setFactor(bestFactor);
          if (smallestDistanceBetweenRaces != Double.MAX_VALUE) {
            distanceBetweenAllRaces += smallestDistanceBetweenRaces;
          }
        }

        // Don't allow this to go on forever. If the improvement is negligeable, then carry on.
        // Not having this check resulted in hugely bloated running times for some periods of HK data (e.g. 2009-04-05).
        // GDS 2009-09-16: This has been profiled for 2009-09-13 in the UK and is observed at 2bn iterations before the profiler lost count. Reduce limit.
//            if (distanceBetweenAllRaces < smallestDistanceBetweenAllRaces && ((smallestDistanceBetweenAllRaces - distanceBetweenAllRaces) > 0.0000000001)) {
        // if the sum of the distance between *all* races improves by less than 1/2000 we can assume that this is close enough. The difference between this
        // limit and letting it run to the previous limit has been observed at 0.00001 in about 1 in 50 ratings; no ranking differences have been observed.
        // The time saving is ~25% runtime when rating the UK for 2009-09-13.
        if (distanceBetweenAllRaces < smallestDistanceBetweenAllRaces && ((smallestDistanceBetweenAllRaces - distanceBetweenAllRaces) > 0.0005)) {
          if (debug) {
            logger.debug("New smallest distance (incrementSize = " + incrementSize + "): " + distanceBetweenAllRaces);
          }
          smallestDistanceBetweenAllRaces = distanceBetweenAllRaces;
        } else {
          distanceImproving = false;
        }
      }

      // Reduce the increment size to tune the factor more finely in future iterations.
      incrementSize *= RACE_RATINGS_INCREMENT_REDUCTION_FACTOR;
    }
  }

}
