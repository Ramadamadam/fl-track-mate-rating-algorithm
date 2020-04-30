package info.track_mate.racing.ratings;

import info.track_mate.racing.ratings.service.RatingsServiceRemote;
import info.track_mate.racing.service.RacingServiceRemote;

import java.util.Collection;

public abstract class AbstractRatingsEngine {
    protected abstract RatingsGenerationStatus intGenerateRatings(RacingServiceRemote racingService, RatingsServiceRemote ratingsService, Collection<Long> ratingsTypeIds,
                                                                  Collection<Long> raceIds, int ratingsCalculationType) throws Exception;

    public abstract void rankRatings(Collection<Long> raceIds, Collection<Long> ratingsTypeIds, RacingServiceRemote racingService, RatingsServiceRemote ratingsService)
      throws Exception;
}
