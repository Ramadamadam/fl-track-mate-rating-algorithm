<?php

namespace Trackmate\RufRatingRewrite;

require_once './algorithm/ruf_rating.php';

use Trackmate\RufRatingRewrite\Algorithm\RufRating;
use function Trackmate\RufRatingRewrite\Algorithm\get_horse_ruf_ratings;

?>

<h2>This the test page</h2>

<hr/>

<?php

$ruf_ratings = get_horse_ruf_ratings();
echo "<pre>";
print_r($ruf_ratings);
echo "</pre>";
?>