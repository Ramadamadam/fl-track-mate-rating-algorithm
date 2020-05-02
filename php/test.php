<?php

namespace Trackmate\RufRatingRewrite;

require_once __DIR__ . '/algorithm/ruf_rating.php';

use Trackmate\RufRatingRewrite\Model\RaceKey;
use function Trackmate\RufRatingRewrite\Algorithm\get_ruf_ratings_for_race_next_day;

?>

<h2>This the test page</h2>

<hr/>



<?php

$race_key = new RaceKey();
$race_key->race_type = 'Handicap';
$race_key->race_name = 'Bet toteplacepot At totesport.com Amateur Riders Handicap (Div 1)';
$race_key->race_class = 'Class 5';
$race_key->race_date = '2019-03-15';
$race_key->race_time = '16:40:00';

$ruf_ratings = get_ruf_ratings_for_race_next_day($race_key, 3);

?>

<h3><?= $race_key->race_name ?> </h3>
<h4><?=$race_key ->race_type ?> , <?=$race_key ->race_class ?> , <?= $race_key->race_date ?> <?= $race_key->race_time ?> </h4>

<table border="1" cellpadding="10px">
    <thead>
    <td>Horse Type</td>
    <td>Horse Name</td>
    <td>Runner Factor</td>
    <td>Race Factor</td>
    <td>Ruf Rating</td>
    </thead>

    <?php foreach ($ruf_ratings as $rating) {

        echo <<<EOT
		<tr>
		            <td>  {$rating->horse_key->horse_type} </td>
		            <td>  {$rating->horse_key->horse_name} </td>
		            <td>  TBD </td>
		            <td>  TBD </td>
		            <td>  TBD </td>
		        </tr>
EOT;
    }

    ?>

</table>
