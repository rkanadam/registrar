<?php

session_start ();
if ($_SESSION["authenticated"] !== true) {
header('Location: unauth/login.php');
return;
}

?>
