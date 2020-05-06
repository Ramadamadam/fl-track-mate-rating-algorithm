<?php


require_once __DIR__ . '/Trackmate/RufRating/Algorithm/RufRatingsEngine.php';

use Trackmate\RufRating\Algorithm\RufRatingsEngine;

?>

<h2>This the test page</h2>


<?php

$race_date_str = $_GET["race_date"];   // such as '2019-03-15';
$race_dates_interval_str = $_GET["race_dates_interval"];   //such as "3 months";

if (empty($race_date_str) or empty($race_dates_interval_str)) {
    echo "<p>Need parameters: race_date, race_dates_interval</p>";
    echo "<p><a href='/test.php?race_date=2019-03-15&race_dates_interval=1%20day'> Example Url</a></p>";
    return;
}


$raceDatesInterval = DateInterval::createFromDateString($race_dates_interval_str);
$raceDate = DateTime::createFromFormat('Y-m-d', $race_date_str);

?>


<h4> Calculation period starts <u> <?= $raceDatesInterval->format("%y years, %m months, %d days") ?></u> before <?= $race_date_str ?> </h4>
<hr/>


<?php
$rufRatingEngine = new RufRatingsEngine();

$start = microtime(true);
$runnerRufRatingArray = $rufRatingEngine->processRacesForUpcomingDay($raceDate, $raceDatesInterval, true);
$time_elapsed_secs = (microtime(true) - $start) / 1000000;
?>

<p>Exec time:  <?= $time_elapsed_secs?> seconds</p>

<hr/>

<table border="1" cellpadding="10px">
    <thead>


    <td>Horse Name</td>
    <td>Race</td>
    <td>Place</td>
    <td>Total Beaten Lengths</td>
    <td>Runner Factor</td>
    <td>Race Factor</td>
    <td>Ruf Rating</td>
    </thead>

    <?php foreach ($runnerRufRatingArray as $entry) {

        echo <<<EOT
        		<tr>       
        		            <td>  {$entry->runner->horse->horse_name} </td>
        		            <td>  {$entry->runner->race->race_key},  {$entry->runner->race->race_distance_adjusted_in_yards} yards, {$entry->runner->race->number_of_runners} runners</td>
        		            <td>  {$entry->runner->place} </td>
        		            <td>  {$entry->runner->total_distance_beat} </td>
        		            <td>  {$entry->runnerFactor} </td>
        		            <td>  {$entry->raceFactor} </td>
        		            <td>  {$entry->rating} </td>
        		        </tr>
        EOT;
    }

    ?>

</table>
