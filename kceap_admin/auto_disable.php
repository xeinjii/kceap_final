<?php
session_start();

if (!isset($_SESSION['admin_id'])) exit;

$type = $_POST['type'] ?? '';
$deadlineFile = __DIR__ . '/deadline.json';

if (!in_array($type,['college','highschool'])) exit;

$settings = json_decode(file_get_contents($deadlineFile), true);
$settings[$type]['disabled'] = true;
file_put_contents($deadlineFile, json_encode($settings, JSON_PRETTY_PRINT));
?>