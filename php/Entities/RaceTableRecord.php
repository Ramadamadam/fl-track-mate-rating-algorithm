<?php

namespace Entities;

class RaceTableRecord
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int|null
     */
    public $addedCard;

    /**
     * @var string|null
     */
    public $addedCardDate;

    /**
     * @var int|null
     */
    public $addedCardBy;

    /**
     * @var int|null
     */
    public $addedResult;

    /**
     * @var string|null
     */
    public $addedResultDate;

    /**
     * @var int|null
     */
    public $addedResultBy;

    /**
     * @var int|null
     */
    public $updated;

    /**
     * @var string|null
     */
    public $updatedDate;

    /**
     * @var int|null
     */
    public $updatedBy;

    /**
     * @var string|null
     */
    public $raceDate;

    /**
     * @var string|null
     */
    public $raceTime;

    /**
     * @var string|null
     */
    public $trackName;

    /**
     * @var string|null
     */
    public $raceName;

    /**
     * @var string|null
     */
    public $raceClass;

    /**
     * @var string|null
     */
    public $raceDistance;

    /**
     * @var string|null
     */
    public $raceDistanceFurlongs;

    /**
     * @var string|null
     */
    public $goingDescription;

    /**
     * @var int|null
     */
    public $prizeMoney;

    /**
     * @var int|null
     */
    public $numberOfRunners;

    /**
     * @var string|null
     */
    public $trackDirection;

    /**
     * @var int|null
     */
    public $cardNumber;

    /**
     * @var string|null
     */
    public $horseName;

    /**
     * @var int|null
     */
    public $horseAge;

    /**
     * @var string|null
     */
    public $horseType;

    /**
     * @var string|null
     */
    public $jockeyName;

    /**
     * @var int|null
     */
    public $jockeyClaim;

    /**
     * @var string|null
     */
    public $trainerName;

    /**
     * @var int|null
     */
    public $stall;

    /**
     * @var int|null
     */
    public $officialRating;

    /**
     * @var int|null
     */
    public $weightPounds;

    /**
     * @var float|null
     */
    public $odds;

    /**
     * @var string|null
     */
    public $form;

    /**
     * @var int|null
     */
    public $days;

    /**
     * @var string|null
     */
    public $headGear;

    /**
     * @var string|null
     */
    public $stallion;

    /**
     * @var string|null
     */
    public $dam;

    /**
     * @var string|null
     */
    public $cd;

    /**
     * @var string|null
     */
    public $raceRestrictionsAge;

    /**
     * @var string|null
     */
    public $raceType;

    /**
     * @var string|null
     */
    public $major;

    /**
     * @var string|null
     */
    public $place;

    /**
     * @var int|null
     */
    public $placingNumerical;

    /**
     * @var string|null
     */
    public $distanceBeat;

    /**
     * @var float|null
     */
    public $totalDistanceBeat;

    /**
     * @var string|null
     */
    public $fav;

    /**
     * @var string|null
     */
    public $comptime;

    /**
     * @var float|null
     */
    public $comptimeNumeric;

    /**
     * @var float|null
     */
    public $medianor;

    /**
     * @var string|null
     */
    public $rcode;

    /**
     * @var float|null
     */
    public $bfsp;

    /**
     * @var float|null
     */
    public $bfspPlace;

    /**
     * @var int|null
     */
    public $placesPaid;

    /**
     * @var int|null
     */
    public $bfPlacesPaid;

    /**
     * @var int|null
     */
    public $yards;

    /**
     * @var int|null
     */
    public $railMove;

    /**
     * @var string|null
     */
    public $comment;

    /**
     * @var string|null
     */
    public $stallPositioning;

    /**
     * @var string
     */
    public $silks;

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int|null $addedCard
     * @return $this
     */
    public function setAddedCard(?int $addedCard)
    {
        $this->addedCard = $addedCard;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAddedCard() : ?int
    {
        return $this->addedCard;
    }

    /**
     * @param string|null $addedCardDate
     * @return $this
     */
    public function setAddedCardDate(?string $addedCardDate)
    {
        $this->addedCardDate = $addedCardDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddedCardDate() : ?string
    {
        return $this->addedCardDate;
    }

    /**
     * @param int|null $addedCardBy
     * @return $this
     */
    public function setAddedCardBy(?int $addedCardBy)
    {
        $this->addedCardBy = $addedCardBy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAddedCardBy() : ?int
    {
        return $this->addedCardBy;
    }

    /**
     * @param int|null $addedResult
     * @return $this
     */
    public function setAddedResult(?int $addedResult)
    {
        $this->addedResult = $addedResult;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAddedResult() : ?int
    {
        return $this->addedResult;
    }

    /**
     * @param string|null $addedResultDate
     * @return $this
     */
    public function setAddedResultDate(?string $addedResultDate)
    {
        $this->addedResultDate = $addedResultDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddedResultDate() : ?string
    {
        return $this->addedResultDate;
    }

    /**
     * @param int|null $addedResultBy
     * @return $this
     */
    public function setAddedResultBy(?int $addedResultBy)
    {
        $this->addedResultBy = $addedResultBy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAddedResultBy() : ?int
    {
        return $this->addedResultBy;
    }

    /**
     * @param int|null $updated
     * @return $this
     */
    public function setUpdated(?int $updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUpdated() : ?int
    {
        return $this->updated;
    }

    /**
     * @param string|null $updatedDate
     * @return $this
     */
    public function setUpdatedDate(?string $updatedDate)
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpdatedDate() : ?string
    {
        return $this->updatedDate;
    }

    /**
     * @param int|null $updatedBy
     * @return $this
     */
    public function setUpdatedBy(?int $updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUpdatedBy() : ?int
    {
        return $this->updatedBy;
    }

    /**
     * @param string|null $raceDate
     * @return $this
     */
    public function setRaceDate(?string $raceDate)
    {
        $this->raceDate = $raceDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceDate() : ?string
    {
        return $this->raceDate;
    }

    /**
     * @param string|null $raceTime
     * @return $this
     */
    public function setRaceTime(?string $raceTime)
    {
        $this->raceTime = $raceTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceTime() : ?string
    {
        return $this->raceTime;
    }

    /**
     * @param string|null $trackName
     * @return $this
     */
    public function setTrackName(?string $trackName)
    {
        $this->trackName = $trackName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrackName() : ?string
    {
        return $this->trackName;
    }

    /**
     * @param string|null $raceName
     * @return $this
     */
    public function setRaceName(?string $raceName)
    {
        $this->raceName = $raceName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceName() : ?string
    {
        return $this->raceName;
    }

    /**
     * @param string|null $raceClass
     * @return $this
     */
    public function setRaceClass(?string $raceClass)
    {
        $this->raceClass = $raceClass;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceClass() : ?string
    {
        return $this->raceClass;
    }

    /**
     * @param string|null $raceDistance
     * @return $this
     */
    public function setRaceDistance(?string $raceDistance)
    {
        $this->raceDistance = $raceDistance;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceDistance() : ?string
    {
        return $this->raceDistance;
    }

    /**
     * @param string|null $raceDistanceFurlongs
     * @return $this
     */
    public function setRaceDistanceFurlongs(?string $raceDistanceFurlongs)
    {
        $this->raceDistanceFurlongs = $raceDistanceFurlongs;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceDistanceFurlongs() : ?string
    {
        return $this->raceDistanceFurlongs;
    }

    /**
     * @param string|null $goingDescription
     * @return $this
     */
    public function setGoingDescription(?string $goingDescription)
    {
        $this->goingDescription = $goingDescription;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGoingDescription() : ?string
    {
        return $this->goingDescription;
    }

    /**
     * @param int|null $prizeMoney
     * @return $this
     */
    public function setPrizeMoney(?int $prizeMoney)
    {
        $this->prizeMoney = $prizeMoney;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrizeMoney() : ?int
    {
        return $this->prizeMoney;
    }

    /**
     * @param int|null $numberOfRunners
     * @return $this
     */
    public function setNumberOfRunners(?int $numberOfRunners)
    {
        $this->numberOfRunners = $numberOfRunners;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberOfRunners() : ?int
    {
        return $this->numberOfRunners;
    }

    /**
     * @param string|null $trackDirection
     * @return $this
     */
    public function setTrackDirection(?string $trackDirection)
    {
        $this->trackDirection = $trackDirection;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrackDirection() : ?string
    {
        return $this->trackDirection;
    }

    /**
     * @param int|null $cardNumber
     * @return $this
     */
    public function setCardNumber(?int $cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCardNumber() : ?int
    {
        return $this->cardNumber;
    }

    /**
     * @param string|null $horseName
     * @return $this
     */
    public function setHorseName(?string $horseName)
    {
        $this->horseName = $horseName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHorseName() : ?string
    {
        return $this->horseName;
    }

    /**
     * @param int|null $horseAge
     * @return $this
     */
    public function setHorseAge(?int $horseAge)
    {
        $this->horseAge = $horseAge;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHorseAge() : ?int
    {
        return $this->horseAge;
    }

    /**
     * @param string|null $horseType
     * @return $this
     */
    public function setHorseType(?string $horseType)
    {
        $this->horseType = $horseType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHorseType() : ?string
    {
        return $this->horseType;
    }

    /**
     * @param string|null $jockeyName
     * @return $this
     */
    public function setJockeyName(?string $jockeyName)
    {
        $this->jockeyName = $jockeyName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getJockeyName() : ?string
    {
        return $this->jockeyName;
    }

    /**
     * @param int|null $jockeyClaim
     * @return $this
     */
    public function setJockeyClaim(?int $jockeyClaim)
    {
        $this->jockeyClaim = $jockeyClaim;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getJockeyClaim() : ?int
    {
        return $this->jockeyClaim;
    }

    /**
     * @param string|null $trainerName
     * @return $this
     */
    public function setTrainerName(?string $trainerName)
    {
        $this->trainerName = $trainerName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainerName() : ?string
    {
        return $this->trainerName;
    }

    /**
     * @param int|null $stall
     * @return $this
     */
    public function setStall(?int $stall)
    {
        $this->stall = $stall;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStall() : ?int
    {
        return $this->stall;
    }

    /**
     * @param int|null $officialRating
     * @return $this
     */
    public function setOfficialRating(?int $officialRating)
    {
        $this->officialRating = $officialRating;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOfficialRating() : ?int
    {
        return $this->officialRating;
    }

    /**
     * @param int|null $weightPounds
     * @return $this
     */
    public function setWeightPounds(?int $weightPounds)
    {
        $this->weightPounds = $weightPounds;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWeightPounds() : ?int
    {
        return $this->weightPounds;
    }

    /**
     * @param float|null $odds
     * @return $this
     */
    public function setOdds(?float $odds)
    {
        $this->odds = $odds;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getOdds() : ?float
    {
        return $this->odds;
    }

    /**
     * @param string|null $form
     * @return $this
     */
    public function setForm(?string $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getForm() : ?string
    {
        return $this->form;
    }

    /**
     * @param int|null $days
     * @return $this
     */
    public function setDays(?int $days)
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDays() : ?int
    {
        return $this->days;
    }

    /**
     * @param string|null $headGear
     * @return $this
     */
    public function setHeadGear(?string $headGear)
    {
        $this->headGear = $headGear;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeadGear() : ?string
    {
        return $this->headGear;
    }

    /**
     * @param string|null $stallion
     * @return $this
     */
    public function setStallion(?string $stallion)
    {
        $this->stallion = $stallion;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStallion() : ?string
    {
        return $this->stallion;
    }

    /**
     * @param string|null $dam
     * @return $this
     */
    public function setDam(?string $dam)
    {
        $this->dam = $dam;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDam() : ?string
    {
        return $this->dam;
    }

    /**
     * @param string|null $cd
     * @return $this
     */
    public function setCd(?string $cd)
    {
        $this->cd = $cd;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCd() : ?string
    {
        return $this->cd;
    }

    /**
     * @param string|null $raceRestrictionsAge
     * @return $this
     */
    public function setRaceRestrictionsAge(?string $raceRestrictionsAge)
    {
        $this->raceRestrictionsAge = $raceRestrictionsAge;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceRestrictionsAge() : ?string
    {
        return $this->raceRestrictionsAge;
    }

    /**
     * @param string|null $raceType
     * @return $this
     */
    public function setRaceType(?string $raceType)
    {
        $this->raceType = $raceType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaceType() : ?string
    {
        return $this->raceType;
    }

    /**
     * @param string|null $major
     * @return $this
     */
    public function setMajor(?string $major)
    {
        $this->major = $major;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMajor() : ?string
    {
        return $this->major;
    }

    /**
     * @param string|null $place
     * @return $this
     */
    public function setPlace(?string $place)
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlace() : ?string
    {
        return $this->place;
    }

    /**
     * @param int|null $placingNumerical
     * @return $this
     */
    public function setPlacingNumerical(?int $placingNumerical)
    {
        $this->placingNumerical = $placingNumerical;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPlacingNumerical() : ?int
    {
        return $this->placingNumerical;
    }

    /**
     * @param string|null $distanceBeat
     * @return $this
     */
    public function setDistanceBeat(?string $distanceBeat)
    {
        $this->distanceBeat = $distanceBeat;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDistanceBeat() : ?string
    {
        return $this->distanceBeat;
    }

    /**
     * @param float|null $totalDistanceBeat
     * @return $this
     */
    public function setTotalDistanceBeat(?float $totalDistanceBeat)
    {
        $this->totalDistanceBeat = $totalDistanceBeat;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalDistanceBeat() : ?float
    {
        return $this->totalDistanceBeat;
    }

    /**
     * @param string|null $fav
     * @return $this
     */
    public function setFav(?string $fav)
    {
        $this->fav = $fav;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFav() : ?string
    {
        return $this->fav;
    }

    /**
     * @param string|null $comptime
     * @return $this
     */
    public function setComptime(?string $comptime)
    {
        $this->comptime = $comptime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComptime() : ?string
    {
        return $this->comptime;
    }

    /**
     * @param float|null $comptimeNumeric
     * @return $this
     */
    public function setComptimeNumeric(?float $comptimeNumeric)
    {
        $this->comptimeNumeric = $comptimeNumeric;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getComptimeNumeric() : ?float
    {
        return $this->comptimeNumeric;
    }

    /**
     * @param float|null $medianor
     * @return $this
     */
    public function setMedianor(?float $medianor)
    {
        $this->medianor = $medianor;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMedianor() : ?float
    {
        return $this->medianor;
    }

    /**
     * @param string|null $rcode
     * @return $this
     */
    public function setRcode(?string $rcode)
    {
        $this->rcode = $rcode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRcode() : ?string
    {
        return $this->rcode;
    }

    /**
     * @param float|null $bfsp
     * @return $this
     */
    public function setBfsp(?float $bfsp)
    {
        $this->bfsp = $bfsp;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBfsp() : ?float
    {
        return $this->bfsp;
    }

    /**
     * @param float|null $bfspPlace
     * @return $this
     */
    public function setBfspPlace(?float $bfspPlace)
    {
        $this->bfspPlace = $bfspPlace;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBfspPlace() : ?float
    {
        return $this->bfspPlace;
    }

    /**
     * @param int|null $placesPaid
     * @return $this
     */
    public function setPlacesPaid(?int $placesPaid)
    {
        $this->placesPaid = $placesPaid;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPlacesPaid() : ?int
    {
        return $this->placesPaid;
    }

    /**
     * @param int|null $bfPlacesPaid
     * @return $this
     */
    public function setBfPlacesPaid(?int $bfPlacesPaid)
    {
        $this->bfPlacesPaid = $bfPlacesPaid;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBfPlacesPaid() : ?int
    {
        return $this->bfPlacesPaid;
    }

    /**
     * @param int|null $yards
     * @return $this
     */
    public function setYards(?int $yards)
    {
        $this->yards = $yards;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getYards() : ?int
    {
        return $this->yards;
    }

    /**
     * @param int|null $railMove
     * @return $this
     */
    public function setRailMove(?int $railMove)
    {
        $this->railMove = $railMove;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRailMove() : ?int
    {
        return $this->railMove;
    }

    /**
     * @param string|null $comment
     * @return $this
     */
    public function setComment(?string $comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment() : ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $stallPositioning
     * @return $this
     */
    public function setStallPositioning(?string $stallPositioning)
    {
        $this->stallPositioning = $stallPositioning;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStallPositioning() : ?string
    {
        return $this->stallPositioning;
    }

    /**
     * @param string $silks
     * @return $this
     */
    public function setSilks(string $silks)
    {
        $this->silks = $silks;
        return $this;
    }

    /**
     * @return string
     */
    public function getSilks() : string
    {
        return $this->silks;
    }
}
