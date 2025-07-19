<?php
// logout.php

session_start();
session_destroy(); // Destroy all session data
header("Location: auth.php");
exit();
?>
