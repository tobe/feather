<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$pattern = '
<div id="entry">

    <div id="content">
        Written by {user}@{hostname} at {nice_time} ({time}), on {date}.<br>
        <h1>
            {nice_title} ({title})
        </h1>
        <p>
            {data}
        </p>
    </div>

    </div>
<hr>
';

require './feather.php';

try {
    $feather = new feather('entries\\', $pattern, NULL);
}catch(Exception $e) {
    echo 'Exception: ' . $e->getMessage();
}

?>
