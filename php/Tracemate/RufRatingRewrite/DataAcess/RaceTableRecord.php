<?php

namespace Trackmate\RufRatingRewrite\DataAccess;


require_once __DIR__ . '/../Model/Models.php';

use Trackmate\RufRatingRewrite\Model\Horse;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;

/**
 *
 * Class RaceTableRecord  Don't use this class directly for business. Convert it to domain models and then use the domain models
 * @package Trackmate\RufRatingRewrite\DataAccess
 */
class RaceTableRecord
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int|null
     */
    public $added_card;

    /**
     * @var string|null
     */
    public $added_card_date;

    /**
     * @var int|null
     */
    public $added_card_by;

    /**
     * @var int|null
     */
    public $added_result;

    /**
     * @var string|null
     */
    public $added_result_date;

    /**
     * @var int|null
     */
    public $added_result_by;

    /**
     * @var int|null
     */
    public $updated;

    /**
     * @var string|null
     */
    public $updated_date;

    /**
     * @var int|null
     */
    public $updated_by;

    /**
     * @var string|null
     */
    public $race_date;

    /**
     * @var string|null
     */
    public $race_time;

    /**
     * @var string|null
     */
    public $track_name;

    /**
     * @var string|null
     */
    public $race_name;

    /**
     * @var string|null
     */
    public $race_class;

    /**
     * @var string|null
     */
    public $race_distance;

    /**
     * @var string|null
     */
    public $race_distance_furlongs;

    /**
     * @var string|null
     */
    public $going_description;

    /**
     * @var int|null
     */
    public $prize_money;

    /**
     * @var int|null
     */
    public $number_of_runners;

    /**
     * @var string|null
     */
    public $track_direction;

    /**
     * @var int|null
     */
    public $card_number;

    /**
     * @var string|null
     */
    public $horse_name;

    /**
     * @var int|null
     */
    public $horse_age;

    /**
     * @var string|null
     */
    public $horse_type;

    /**
     * @var string|null
     */
    public $jockey_name;

    /**
     * @var int|null
     */
    public $jockey_claim;

    /**
     * @var string|null
     */
    public $trainer_name;

    /**
     * @var int|null
     */
    public $stall;

    /**
     * @var int|null
     */
    public $official_rating;

    /**
     * @var int|null
     */
    public $weight_pounds;

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
    public $head_gear;

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
    public $race_restrictions_age;

    /**
     * @var string|null
     */
    public $race_type;

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
    public $placing_numerical;

    /**
     * @var string|null
     */
    public $distance_beat;

    /**
     * @var float|null
     */
    public $total_distance_beat;

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
    public $comptime_numeric;

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
    public $bfsp_place;

    /**
     * @var int|null
     */
    public $places_paid;

    /**
     * @var int|null
     */
    public $bf_places_paid;

    /**
     * @var int|null
     */
    public $yards;

    /**
     * @var int|null
     */
    public $rail_move;

    /**
     * @var string|null
     */
    public $comment;

    /**
     * @var string|null
     */
    public $stall_positioning;

    /**
     * @var string
     */
    public $silks;


    /**
     * @return RaceRunner
     */
    public function toRaceRunner(): RaceRunner
    {
        $raceRunner = new RaceRunner();
        $raceRunner->id = $this->id;
        $raceRunner->horse = $this->toHorse();
        $raceRunner->race = $this->toRace();

        $raceRunner->placing_numerical = $this->placing_numerical;
        $raceRunner->place = $this->place;

        $raceRunner->total_distance_beat = $this->to_real_total_distance_beat($this->distance_beat, $this->total_distance_beat);
        return $raceRunner;
    }

    /**
     * Extract runner recorders
     * @param array $table_records
     * @return array|RaceRunner[]
     */
    public static function extractRaceRunners(array $table_records): array
    {
        $raceRunners = array_map(function (RaceTableRecord $table_record) {
            return $table_record->toRaceRunner();
        }, $table_records);

        return $raceRunners;
    }


    private function toRace(): Race
    {
        $race = new Race();
        $race->race_key = $this->toRaceKey();
        $race->race_type = $this->race_type;
        $race->race_name = $this->race_name;
        $race->race_class = $this->race_class;
        $race->number_of_runners = $this->number_of_runners;
        $race->race_distance_adjusted_in_yards = $this->yards + $this->rail_move;
        return $race;
    }

    private function to_real_total_distance_beat( ?string $distance_beat, ?float $total_distance_beat)
    {
        if($total_distance_beat == 0 && !empty($distance_beat)){
            //the string values are worked out by sql:  select distinct distance_beat from ajr_trackmate_all where distance_beat regexp  '^[^0-9]';
            if ($distance_beat === 'HD') {
                return 0.1;
            }
            if ($distance_beat === 'SH') {
                return 0.06;
            }
            if ($distance_beat === 'NK') {
                return 0.25;
            }
            if ($distance_beat === 'NSE') {
                return 0.03;
            }
            if ($distance_beat === 'DH') {
                return 0;
            }
            return 0; //none of them? It's just bad data. Make it 0;
        }

        return $total_distance_beat;
    }

    private function toRaceKey(): RaceKey
    {
        $race_key = new RaceKey();
        $race_key->track_name = $this->track_name;
        $race_key->race_date = $this->race_date;
        $race_key->race_time = $this->race_time;
        return $race_key;
    }

    private function toHorse()
    {
        $horse = new Horse();
        $horse->horse_name = $this->horse_name;
        return $horse;
    }


}


