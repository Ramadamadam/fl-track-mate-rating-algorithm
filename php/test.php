<?php


require_once __DIR__ . '/Tracemate/RufRatingRewrite/Algorithm/ruf_rating.php';

use Trackmate\RufRatingRewrite\Model\RaceKey;
use function Trackmate\RufRatingRewrite\Algorithm\get_ruf_ratings_for_race_next_day;

?>

<h2>This the test page</h2>



<?php

$race_key = new RaceKey();
$race_key->track_name = $_GET["track_name"]; // such as  'Chelmsford City';
$race_key->race_date = $_GET["race_date"];   // such as '2019-03-15';
$race_key->race_time = $_GET["race_time"];   // such as '16:40:00';
$race_dates_interval_str = $_GET["race_dates_interval"];   //such as "6 months";


if(empty($race_key->track_name) or empty($race_key->race_date) or empty($race_key->race_time) or empty($race_dates_interval_str)){
    echo "<p>Need parameters: track_name, race_date, race_time, race_dates_interval</p>";
    echo "<p><a href='/test.php?track_name=Chelmsford%20City&race_date=2019-03-15&race_time=16:40:00&race_dates_interval=6%20months'> Example Url</a></p>";
    return;
}


$race_dates_interval = DateInterval::createFromDateString($race_dates_interval_str);







$ruf_rating_final_result = get_ruf_ratings_for_race_next_day($race_key, $race_dates_interval);
$target_race = $ruf_rating_final_result->target_race;
$result_entries = $ruf_rating_final_result->entries->values(); //type is DS\Sequence
?>
<h3>Target race: <?= $target_race->race_name ?>, <?= $target_race->race_type ?> , <?= $target_race->race_class ?>
    , <?= $target_race->race_key->race_date ?> <?= $target_race->race_key->race_time ?>  </h3>

<h4> Calculation period starts <u> <?= $race_dates_interval->format("%y years, %m months, %d days") ?></u> before <?= $target_race->race_key->race_date ?>
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

            <?php foreach ($result_entries as $entry) {

                echo <<<EOT
        		<tr>       
        		            <td>  {$entry->race_runner->horse->horse_name} </td>
        		            <td>  {$entry->race_runner->race->race_key},  {$entry->race_runner->race->race_distance_adjusted_in_yards} yards, {$entry->race_runner->race->number_of_runners} runners</td>
        		            <td>  {$entry->race_runner->place} </td>
        		            <td>  {$entry->race_runner->total_distance_beat} </td>
        		            <td>  {$entry->runner_factor} </td>
        		            <td>  {$entry->race_factor} </td>
        		            <td>  {$entry->rating} </td>
        		        </tr>
        EOT;
            }

            ?>

    </table>
