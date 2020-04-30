package info.track_mate.racing.ratings;

import info.track_mate.racing.domain.IRace;
import info.track_mate.racing.domain.IRunner;
import info.track_mate.racing.ratings.service.RatingsServiceRemote;

import java.util.Collection;
import java.util.Date;

public class RatingsRaceValidityChecker {
    public RatingsRaceValidityChecker(long ratingsTypeId, RatingsServiceRemote ratingsService) {
    }

    public Integer getCalendarModifactionUnit() {
        return 0;
    }

    public Integer getCalendarModifactionQuantity() {
        return 0;
    }

    public void setTargetRace(IRace targetRace) {

    }

    public String getRatingsPeriodString() {
        return null;
    }

    public void setHorseRunners(Collection<IRunner> horseRunners) {
    }

    public boolean isRaceWithinRatingsPeriod(Date thisRaceDate, long id) {
        return false;
    }
}
