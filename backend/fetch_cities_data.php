<?php
session_start();
require_once __DIR__ . '/../config.php';

try {
    // ✅ Create a PDO connection
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Fetch countries
    $stmt = $pdo->query("SELECT id, name FROM countries");
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as an associative array

    $user_role        = $_SESSION['user_role'] ?? '';
    $city_id          = $_SESSION['city'] ?? '';
    $selected_city_id = null;

    // ✅ Fetch cities
    $query = "SELECT id,city_name FROM cities";

    if (!empty($user_role) && in_array($user_role, ['manager','data_entry_agent']) && !empty($city_id)) {
        $query .= " where id = $city_id";
        $selected_city_id = $city_id;
    }
    $stmt = $pdo->query($query);
//    die($query);
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as a simple array

    // ✅ Send JSON response
    // echo json_encode(['countries' => $countries, 'cities' => $cities]);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage()); // Log error for security
    echo json_encode(['error' => 'A database error occurred.']);
}
