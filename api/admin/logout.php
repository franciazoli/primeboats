<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../response.php';

session_start();
session_destroy();

json_ok(['message' => 'Logged out']);
