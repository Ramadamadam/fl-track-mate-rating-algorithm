<?php

namespace Trackmate\RufRatingRewrite\DataAccess;

use DateTime;
use PDO;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use Trackmate\RufRatingRewrite\Model\RaceRunner;

require_once __DIR__ . '/../Model/Models.php';
require_once __DIR__ . '/RaceTableRecord.php';

class RufRatingDataAccess
{

    /**
     * @param RaceKey $race_key
     * @return array | RaceRunner[]
     *
     */
    public function getRaceRunnersByRaceKey(RaceKey $race_key): array
    {
        return RaceTableRecord::extractRaceRunners($this->getTableRecordsByRaceKey($race_key));
    }


    /**
     * @param DateTime $start_date
     * @param DateTime $end_date
     * @param array $horse_array | Horse[]
     * @return array | RaceRunner[]
     */
    public function getRaceRunnersByHorsesBetween(DateTime $start_date, DateTime $end_date, array $horse_array)
    {

        $horse_names = array_map(fn($horse) => $horse->horse_name, $horse_array);
        $horse_name_question_marks_str = join(',', array_map(fn($horse_name) => '?', $horse_names));


        $pdo = null;
        try {
            $pdo = $this->getPdo();
            $sql = <<< EOD
            select * from ajr_trackmate_all where (race_date between ? and ?) 
                and horse_name in ($horse_name_question_marks_str);
        EOD;

            $params = [];
            array_push($params, date_format($start_date, "Y-m-d"));
            array_push($params, date_format($end_date, "Y-m-d"));
            foreach ($horse_names as $horse_name){
                array_push($params, $horse_name);
            }



            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $table_records =  $stmt->fetchAll(PDO::FETCH_CLASS, RaceTableRecord::class);
            return RaceTableRecord::extractRaceRunners($table_records);
        } finally {
            $pdo = null;
        }

    }

    /**
     * @param RaceKey $race_key
     * @return array|RaceTableRecord[]
     */
    private function getTableRecordsByRaceKey(RaceKey $race_key): array
    {

        $pdo = null;
        try {
            $pdo = $this->getPdo();
            $sql = <<< EOD
            select * from ajr_trackmate_all 
                where track_name = ? 
                and race_date = ?  
                and race_time = ?;
        EOD;

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $race_key->track_name,
                $race_key->race_date,
                $race_key->race_time
            ]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, RaceTableRecord::class);

        } finally {
            $pdo = null;
        }


    }

    /**
     * @return PDO
     */
    private function getPdo(): PDO
    {
        $host = '127.0.0.1';
        $db = 'track_mate_test';
        $user = 'track_mate_user';
        $pass = 'abc123';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    }


}


?>
