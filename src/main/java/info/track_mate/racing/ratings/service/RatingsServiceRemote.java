package info.track_mate.racing.ratings.service;

import info.track_mate.racing.ratings.domain.IRatingsValueSet;
import info.track_mate.util.PageByPageDefinition;

import java.util.Collection;

public class RatingsServiceRemote {

    public Collection<IRatingsValueSet> list(Class<IRatingsValueSet> iRatingsValueSetClass, PageByPageDefinition pbpDef) {
        return null;
    }

    public void createOrUpdateEntity(Class<IRatingsValueSet> iRatingsValueSetClass, IRatingsValueSet ratingsValueSet, Object o) {
    }

    public void storeRatings(IRatingsValueSet ratingsSet, Object o, Long ratingsTypeId, long id) {
    }
}
