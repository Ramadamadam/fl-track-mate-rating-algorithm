<?php

require_once __DIR__ . '/Tracemate/RufRatingRewrite/Algorithm/ruf_rating.php';

use Trackmate\RufRatingRewrite\Algorithm\RufRatingsResult;
use Trackmate\RufRatingRewrite\Model\RaceKey;
use function Trackmate\RufRatingRewrite\Algorithm\get_ruf_ratings_for_race_next_day;

?>

<h2>This the test page</h2>



<?php

$race_key = new RaceKey();
$race_key->track_name = 'Chelmsford City';
$race_key->race_date = '2019-03-15';
$race_key->race_time = '16:40:00';

$race_dates_interval = DateInterval::createFromDateString("6 months");
$length_per_furlong = 82.5;

$ruf_ratings_result = get_ruf_ratings_for_race_next_day($race_key, $race_dates_interval, $length_per_furlong);
$target_race = $ruf_ratings_result->target_race;
$ruf_ratings = $ruf_ratings_result->relatedRaceRatings;
?>
<h3>Target race: <?= $target_race -> race_name ?>, <?= $target_race->race_type ?> , <?= $target_race->race_class ?> , <?= $target_race->race_key->race_date ?> <?= $target_race->race_key->race_time ?>  </h3>

<h4> Calculation period starts <u> <?= $race_dates_interval -> format("%y years, %m months, %d days") ?></u>   before <?= $target_race->race_key->race_date  ?>
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

    <?php foreach ($ruf_ratings as $rating) {

        echo <<<EOT
		<tr>		   
		        
		            <td>  {$rating->race_runner->horse->horse_name} </td>
		            <td>  {$rating->race_runner->race->race_key} </td>
		            <td>  {$rating->race_runner_factor} </td>
		            <td>  TBD </td>
		            <td>  TBD </td>
		        </tr>
EOT;
    }

    ?>

</table>
