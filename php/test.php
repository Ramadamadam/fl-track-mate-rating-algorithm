<?php

require_once __DIR__ . '/Tracemate/RufRatingRewrite/Algorithm/ruf_rating.php';

use Trackmate\RufRatingRewrite\Model\RaceKey;
use function Trackmate\RufRatingRewrite\Algorithm\get_ruf_ratings_for_race_next_day;

?>

<h2>This the test page</h2>


<?php

$race_key = new RaceKey();
$race_key->track_name = 'Chelmsford City';
$race_key->race_date = '2019-03-15';
$race_key->race_time = '16:40:00';

$race_dates_interval = DateInterval::createFromDateString("6 month");
$length_per_furlong = 82.5;

$ruf_rating_final_result = get_ruf_ratings_for_race_next_day($race_key, $race_dates_interval, $length_per_furlong);
$target_race = $ruf_rating_final_result->target_race;
$result_entries = $ruf_rating_final_result->entries->values(); //type is DS\Sequence
?>
<h3>Target race: <?= $target_race->race_name ?>, <?= $target_race->race_type ?> , <?= $target_race->race_class ?>
    , <?= $target_race->race_key->race_date ?> <?= $target_race->race_key->race_time ?>  </h3>

<h4> Calculation period starts <u> <?= $race_dates_interval->format("%y years, %m months, %d days") ?></u> before <?= $target_race->race_key->race_date ?>
    <h4> 1 furlong = <?= $length_per_furlong ?> lengths. </h4>

    <hr/>

    <table border="1" cellpadding="10px">
        <thead>


        <td>Horse Name</td>
        <td>Race</td>
        <td>Runner Factor</td>
        <td>Race Factor</td>
        <td>Ruf Rating</td>
        </thead>

            <?php foreach ($result_entries as $entry) {

                echo <<<EOT
        		<tr>       
        		            <td>  {$entry->race_runner->horse->horse_name} </td>
        		            <td>  {$entry->race_runner->race->race_key} </td>
        		            <td>  {$entry->runner_factor} </td>
        		            <td>  {$entry->race_factor} </td>
        		            <td>  {$entry->rating} </td>
        		        </tr>
        EOT;
            }

            ?>

    </table>
