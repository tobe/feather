<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$pattern = '
<div id="entry">
    <div id="content">
        <h2>
            {nice_title} ({title})
        </h2>
        <p style="font-size: 10px;">Written by {user}@{hostname} at {nice_time} ({time}), on {date}.</p>
        <p>
            {data}
        </p>
    </div>
</div>
<hr><br><br><br>
';

require './feather.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Example</title>
</head>
    <body>
    <?php
        try {
            $feather = new feather('entries\\', $pattern);
        }catch(Exception $e) {
            echo 'Exception: ' . $e->getMessage();
        }
    ?>
    </body>
</html>
