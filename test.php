<?php

$base = dirname($_SERVER["SCRIPT_FILENAME"]);
require_once "$base/vendor/autoload.php";
require_once "$base/server/util/init_once.php";

?>
<?= phpinfo() ?>
