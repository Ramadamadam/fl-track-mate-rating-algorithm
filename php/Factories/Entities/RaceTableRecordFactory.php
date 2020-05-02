<?php

namespace Factories\Entities;

use kristijorgji\DbToPhp\Data\AbstractEntityFactory;
use Entities\RaceTableRecord;

class RaceTableRecordFactory extends AbstractEntityFactory
{
    /**
     * @var array
     */
    protected static $fields = [
        'id',
        'added_card',
        'added_card_date',
        'added_card_by',
        'added_result',
        'added_result_date',
        'added_result_by',
        'updated',
        'updated_date',
        'updated_by',
        'race_date',
        'race_time',
        'track_name',
        'race_name',
        'race_class',
        'race_distance',
        'race_distance_furlongs',
        'going_description',
        'prize_money',
        'number_of_runners',
        'track_direction',
        'card_number',
        'horse_name',
        'horse_age',
        'horse_type',
        'jockey_name',
        'jockey_claim',
        'trainer_name',
        'stall',
        'official_rating',
        'weight_pounds',
        'odds',
        'form',
        'days',
        'head_gear',
        'stallion',
        'dam',
        'cd',
        'race_restrictions_age',
        'race_type',
        'major',
        'place',
        'placing_numerical',
        'distance_beat',
        'total_distance_beat',
        'fav',
        'comptime',
        'comptime_numeric',
        'medianor',
        'rcode',
        'bfsp',
        'bfsp_place',
        'places_paid',
        'bf_places_paid',
        'yards',
        'rail_move',
        'comment',
        'stall_positioning',
        'silks',
    ];

    /**
     * @param array $data
     * @return RaceTableRecord
     */
    public static function make(array $data = []) : RaceTableRecord
    {
        return self::makeFromData(self::makeData($data));
    }

    /**
     * @param array $data
     * @return RaceTableRecord
     */
    public static function makeFromData(array $data) : RaceTableRecord
    {
        self::validateData($data);
        return self::mapArrayToEntity($data, RaceTableRecord::class);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function makeData(array $data = []) : array
    {
        self::validateData($data);
        return [
            'id' => array_key_exists('id', $data) ?
                $data['id'] : self::randomInt24(),
            'added_card' => array_key_exists('added_card', $data) ?
                $data['added_card'] : self::randomInt32(),
            'added_card_date' => array_key_exists('added_card_date', $data) ?
                $data['added_card_date'] : self::randomString(rand(0, 64)),
            'added_card_by' => array_key_exists('added_card_by', $data) ?
                $data['added_card_by'] : self::randomInt32(),
            'added_result' => array_key_exists('added_result', $data) ?
                $data['added_result'] : self::randomInt32(),
            'added_result_date' => array_key_exists('added_result_date', $data) ?
                $data['added_result_date'] : self::randomString(rand(0, 64)),
            'added_result_by' => array_key_exists('added_result_by', $data) ?
                $data['added_result_by'] : self::randomInt32(),
            'updated' => array_key_exists('updated', $data) ?
                $data['updated'] : self::randomInt32(),
            'updated_date' => array_key_exists('updated_date', $data) ?
                $data['updated_date'] : self::randomString(rand(0, 64)),
            'updated_by' => array_key_exists('updated_by', $data) ?
                $data['updated_by'] : self::randomInt32(),
            'race_date' => array_key_exists('race_date', $data) ?
                $data['race_date'] : self::randomString(rand(0, 64)),
            'race_time' => array_key_exists('race_time', $data) ?
                $data['race_time'] : self::randomString(rand(0, 64)),
            'track_name' => array_key_exists('track_name', $data) ?
                $data['track_name'] : self::randomString(rand(0, 50)),
            'race_name' => array_key_exists('race_name', $data) ?
                $data['race_name'] : self::randomString(rand(0, 250)),
            'race_class' => array_key_exists('race_class', $data) ?
                $data['race_class'] : self::randomString(rand(0, 20)),
            'race_distance' => array_key_exists('race_distance', $data) ?
                $data['race_distance'] : self::randomString(rand(0, 10)),
            'race_distance_furlongs' => array_key_exists('race_distance_furlongs', $data) ?
                $data['race_distance_furlongs'] : self::randomString(rand(0, 6)),
            'going_description' => array_key_exists('going_description', $data) ?
                $data['going_description'] : self::randomString(rand(0, 25)),
            'prize_money' => array_key_exists('prize_money', $data) ?
                $data['prize_money'] : self::randomInt24(),
            'number_of_runners' => array_key_exists('number_of_runners', $data) ?
                $data['number_of_runners'] : self::randomInt32(),
            'track_direction' => array_key_exists('track_direction', $data) ?
                $data['track_direction'] : self::randomString(rand(0, 20)),
            'card_number' => array_key_exists('card_number', $data) ?
                $data['card_number'] : self::randomInt32(),
            'horse_name' => array_key_exists('horse_name', $data) ?
                $data['horse_name'] : self::randomString(rand(0, 50)),
            'horse_age' => array_key_exists('horse_age', $data) ?
                $data['horse_age'] : self::randomInt32(),
            'horse_type' => array_key_exists('horse_type', $data) ?
                $data['horse_type'] : self::randomString(rand(0, 20)),
            'jockey_name' => array_key_exists('jockey_name', $data) ?
                $data['jockey_name'] : self::randomString(rand(0, 50)),
            'jockey_claim' => array_key_exists('jockey_claim', $data) ?
                $data['jockey_claim'] : self::randomInt32(),
            'trainer_name' => array_key_exists('trainer_name', $data) ?
                $data['trainer_name'] : self::randomString(rand(0, 50)),
            'stall' => array_key_exists('stall', $data) ?
                $data['stall'] : self::randomInt32(),
            'official_rating' => array_key_exists('official_rating', $data) ?
                $data['official_rating'] : self::randomInt32(),
            'weight_pounds' => array_key_exists('weight_pounds', $data) ?
                $data['weight_pounds'] : self::randomInt32(),
            'odds' => array_key_exists('odds', $data) ?
                $data['odds'] : self::randomFloat(),
            'form' => array_key_exists('form', $data) ?
                $data['form'] : self::randomString(rand(0, 10)),
            'days' => array_key_exists('days', $data) ?
                $data['days'] : self::randomInt32(),
            'head_gear' => array_key_exists('head_gear', $data) ?
                $data['head_gear'] : self::randomString(rand(0, 20)),
            'stallion' => array_key_exists('stallion', $data) ?
                $data['stallion'] : self::randomString(rand(0, 50)),
            'dam' => array_key_exists('dam', $data) ?
                $data['dam'] : self::randomString(rand(0, 50)),
            'cd' => array_key_exists('cd', $data) ?
                $data['cd'] : self::randomString(rand(0, 5)),
            'race_restrictions_age' => array_key_exists('race_restrictions_age', $data) ?
                $data['race_restrictions_age'] : self::randomString(rand(0, 5)),
            'race_type' => array_key_exists('race_type', $data) ?
                $data['race_type'] : self::randomString(rand(0, 25)),
            'major' => array_key_exists('major', $data) ?
                $data['major'] : self::randomString(rand(0, 20)),
            'place' => array_key_exists('place', $data) ?
                $data['place'] : self::randomString(rand(0, 4)),
            'placing_numerical' => array_key_exists('placing_numerical', $data) ?
                $data['placing_numerical'] : self::randomInt32(),
            'distance_beat' => array_key_exists('distance_beat', $data) ?
                $data['distance_beat'] : self::randomString(rand(0, 6)),
            'total_distance_beat' => array_key_exists('total_distance_beat', $data) ?
                $data['total_distance_beat'] : self::randomFloat(),
            'fav' => array_key_exists('fav', $data) ?
                $data['fav'] : self::randomString(rand(0, 8)),
            'comptime' => array_key_exists('comptime', $data) ?
                $data['comptime'] : self::randomString(rand(0, 14)),
            'comptime_numeric' => array_key_exists('comptime_numeric', $data) ?
                $data['comptime_numeric'] : self::randomFloat(),
            'medianor' => array_key_exists('medianor', $data) ?
                $data['medianor'] : self::randomFloat(),
            'rcode' => array_key_exists('rcode', $data) ?
                $data['rcode'] : self::randomString(rand(0, 25)),
            'bfsp' => array_key_exists('bfsp', $data) ?
                $data['bfsp'] : self::randomFloat(),
            'bfsp_place' => array_key_exists('bfsp_place', $data) ?
                $data['bfsp_place'] : self::randomFloat(),
            'places_paid' => array_key_exists('places_paid', $data) ?
                $data['places_paid'] : self::randomInt32(),
            'bf_places_paid' => array_key_exists('bf_places_paid', $data) ?
                $data['bf_places_paid'] : self::randomInt32(),
            'yards' => array_key_exists('yards', $data) ?
                $data['yards'] : self::randomInt32(),
            'rail_move' => array_key_exists('rail_move', $data) ?
                $data['rail_move'] : self::randomInt32(),
            'comment' => array_key_exists('comment', $data) ?
                $data['comment'] : self::randomString(rand(0, 64)),
            'stall_positioning' => array_key_exists('stall_positioning', $data) ?
                $data['stall_positioning'] : self::randomString(rand(0, 20)),
            'silks' => array_key_exists('silks', $data) ?
                $data['silks'] : self::randomString(rand(0, 25)),
        ];
    }
}
