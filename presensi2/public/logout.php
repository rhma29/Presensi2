<?php
session_start();
session_unset();
session_destroy();

// ✅ Setelah logout langsung ke halaman login
header("Location: ../public/login.php");
exit;
