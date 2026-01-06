<?php
require_once __DIR__ . '/config.php';

startSession();
session_unset();
session_destroy();

header('Location: index.php');
exit();
