<?php

require_once __DIR__ . '/Trackmate/RufRating/Algorithm/RufRatingsEngine.php';

use Trackmate\RufRating\Algorithm\RufRatingsEngine;

?>

<h2>This the test page</h2>


<?php

$rufRatingEngine = new RufRatingsEngine();


$raceDate = DateTime::createFromFormat('Y-m-d', '2018-12-02');
$raceDatesInterval = DateInterval::createFromDateString('1 day');
$rufRatingEngine->processRacesForDate($raceDate, $raceDatesInterval);

?>
