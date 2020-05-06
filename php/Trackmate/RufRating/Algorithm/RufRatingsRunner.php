<?php


namespace Trackmate\RufRating\Algorithm;




use DateTime;

class RufRatingsRunner
{
    private float $rating;
    private DateTime $date;
    private string $horseName;

    /**
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getHorseName(): string
    {
        return $this->horseName;
    }

    /**
     * @param string $horseName
     */
    public function setHorseName(string $horseName): void
    {
        $this->horseName = $horseName;
    }


}