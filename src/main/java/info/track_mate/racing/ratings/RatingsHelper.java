package info.track_mate.racing.ratings;

import info.track_mate.racing.domain.IRace;
import info.track_mate.racing.service.RacingServiceRemote;
import info.track_mate.util.DefaultValueObjectDefinition;

import java.util.Collection;
import java.util.Date;

public class RatingsHelper {

    public static Collection<IRace> getRacesByMarketAndPeriod(Long key, Date periodStartDate, Date periodEndDate, DefaultValueObjectDefinition valueObjectDefinition, RacingServiceRemote racingService) {
        return null;
    }

    public static boolean isCompatibleRaceType(FullRaceType thisRaceType, FullRaceType relatedRaceType) {
        return false;
    }

    public static class FullRaceType {

        public static FullRaceType getFullRaceType(IRace periodRace) {
            return null;
        }
    }
}
