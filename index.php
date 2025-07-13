<?php
// --- CORS Headers ---
header("Access-Control-Allow-Origin: *"); // Allow all origins or specify exact domain
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



require_once 'routes/api.php';
require_once 'routes/users.php';

