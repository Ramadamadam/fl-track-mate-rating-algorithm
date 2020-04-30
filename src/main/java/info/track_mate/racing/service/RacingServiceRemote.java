package info.track_mate.racing.service;

import info.track_mate.racing.domain.IMarket;
import info.track_mate.racing.domain.IRace;
import info.track_mate.racing.domain.IRunner;
import info.track_mate.util.DefaultValueObjectDefinition;
import info.track_mate.util.PageByPageDefinition;

import java.util.Collection;

public class RacingServiceRemote {
    public Collection<IRace> getRaces(Collection<Long> raceIds, String vodefAllAttributes) {
        return null;
    }

    public IMarket getMarketForRace(long id, DefaultValueObjectDefinition def) {
        return null;
    }

    public Collection<IRunner> getRunners(long id, DefaultValueObjectDefinition valueObjectDefinition) {
        return null;
    }

    public Collection<IRunner> list(Class<IRunner> iRunnerClass, PageByPageDefinition pbpDef) {
        return null;
    }
}
