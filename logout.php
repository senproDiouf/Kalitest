<?php
require_once __DIR__ . '/config/config.php';

session_destroy();
header('Location: /KaliTest/login.php');
exit;
