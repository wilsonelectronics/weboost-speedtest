<?php

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

require 'telemetry_settings.php';
require_once 'telemetry_db.php';

$email = trim((string) ($_POST['email'] ?? ''));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'A valid email address is required.']);
    exit;
}

$result = deleteSpeedtestResultsByEmail($email, true);

if ($result instanceof Exception) {
    error_log('delete_by_email.php deleteSpeedtestResultsByEmail failed: '.$result->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An error occurred while processing your request.']);
    exit;
}

if (false === $result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An error occurred while processing your request.']);
    exit;
}

echo json_encode(['success' => true, 'deleted' => $result]);
