<?php
session_start();
session_unset();     // Clear all session variables
session_destroy();   // Destroy the session

// Optional: Clear cookies (if you set any manually)
// setcookie("your_cookie_name", "", time() - 3600);

header("Location: index.html");
exit;
