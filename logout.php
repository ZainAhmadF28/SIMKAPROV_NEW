<?php
session_start();
// Hancurkan semua session
session_unset();
session_destroy();

// Redirect ke halaman index.php (halaman login)
header("Location: index.php");
exit();
?>
