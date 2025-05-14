<?php
require_once __DIR__ . '/../config.php'; // Adjust path if needed

$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "UPDATE visa_applications 
          SET application_limit = application_limit - 1 
          WHERE application_limit > 0 
          AND DATE(created_at) < CURDATE()";

$stmt = $pdo->prepare($query);
$stmt->execute();

echo "Application limits updated.";
