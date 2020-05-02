<?php
include_once 'ruf_ratings.php';

echo "This the test page"
?>

<hr/>

<?php
    $ruf_ratings = get_horse_ruf_ratings();
    echo "<pre>";
    print_r($ruf_ratings);
    echo "</pre>";
?>