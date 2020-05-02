<?php

namespace Trackmate\RufRatingRewrite\DataAccess;


require_once __DIR__ . '/../Model/Models.php';

use Trackmate\RufRatingRewrite\Model\HorseKey;
use Trackmate\RufRatingRewrite\Model\Race;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;

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
     * Extract runner recorders
     * @param array $single_race_table_records
     * @return array|RaceRunner[]
     */
    public static function extractRaceRunnersOfSingleRace(array $single_race_table_records): array
    {
        $raceRunners = array_map(function (RaceTableRecord $table_record) {
            $raceRunner = new RaceRunner();
            $raceRunner->horse_key = $table_record->toHorseKey();
            $raceRunner->placing_numerical = $table_record->placing_numerical;
            $raceRunner->place = $table_record->place;
            $raceRunner->total_distance_beat = $table_record->total_distance_beat;
            return $raceRunner;
        }, $single_race_table_records);

        return $raceRunners;
    }

    public function toHorseKey(): HorseKey
    {
        $horse_key = new HorseKey();
        $horse_key->horse_name = $this->horse_name;
        $horse_key->horse_type = $this->horse_type;
        return $horse_key;
    }


    public static function extractRace(RaceTableRecord $table_record): Race
    {
        $race = new Race();
        $race->race_key = $table_record->toRaceKey();
        $race->race_distance_furlongs = $table_record->race_distance_furlongs;
        return $race;
    }


    public function toRaceKey(): RaceKey
    {
        $race_key = new RaceKey();
        $race_key->race_type = $race_key->race_type;
        $race_key->race_name = $race_key->race_name;
        $race_key->race_class = $race_key->race_class;
        $race_key->race_date = $race_key->race_date;
        $race_key->race_time = $race_key->race_time;
        return $race_key;
    }
}


