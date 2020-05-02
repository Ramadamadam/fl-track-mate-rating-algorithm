<?php

namespace Trackmate\RufRatingRewrite;

require_once './algorithm/ruf_rating.php';

use Trackmate\RufRatingRewrite\Algorithm\RufRating;
use function Trackmate\RufRatingRewrite\Algorithm\get_ruf_ratings_for_race_next_day;

?>

<h2>This the test page</h2>

<hr/>

<?php

$ruf_ratings = get_ruf_ratings_for_race_next_day();
echo "<pre>";
print_r($ruf_ratings);
echo "</pre>";
?>