<?php
require_once __DIR__ . '/../config/database.php';
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);
header("Location: /Kamadhenu-goushala/admin/login.php");
exit;
