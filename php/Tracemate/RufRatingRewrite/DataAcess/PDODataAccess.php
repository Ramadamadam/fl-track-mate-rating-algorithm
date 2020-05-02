<?php

namespace Trackmate\RufRatingRewrite\DataAccess;

use PDO;

require_once __DIR__ . '/../Model/Models.php';
use Trackmate\RufRatingRewrite\Model\RaceKey;

require_once __DIR__ . '/RaceTableRecord.php';
use Trackmate\RufRatingRewrite\DataAccess\RaceTableRecord;


/**
 * @param RaceKey $race_key
 * @return array|RaceTableRecord[]
 */
function get_table_records_by_race_key(RaceKey $race_key): array
{

    $pdo = null;
    try {
        $pdo = get_pdo();
        $sql = <<< 'EOD'
            select * from ajr_trackmate_all 
                where race_type = ? 
                and race_name = ?  
                and race_class = ?  
                and race_date = ?  
                and race_time = ?;
        EOD;

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $race_key->race_type,
            $race_key->race_name,
            $race_key->race_class,
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
function get_pdo(): PDO
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


?>
