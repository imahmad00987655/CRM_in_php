<?php
session_start();
require_once __DIR__ . '/../config.php';

// Get the city parameter
$city = $_GET['city'];
$agents = [];
$data_entry_agents = [];

try {
    // ✅ Create a PDO connection
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare statement to fetch both visa agents and data entry agents in a single query
    $stmt = $pdo->prepare("
    SELECT 
        u.name, 
        u.id, 
        r.role_name AS user_role, 
        c.city_name 
    FROM users u
    JOIN cities c ON u.city = c.id
    JOIN user_roles r ON u.user_role = r.id
    WHERE r.role_name IN ('visa_agent', 'data_entry_agent','sales_agent','manager') 
    AND c.id = :city
");

    $stmt->execute(['city' => $city]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate results into respective categories
    $agents = [];
    $data_entry_agents = [];

    foreach ($results as $row) {
        if ($row['user_role'] === 'visa_agent') {
            $agents[] = $row;
        } elseif ($row['user_role'] === 'data_entry_agent') {
            $data_entry_agents[] = $row;
        }
    }

    // Send JSON response
    $response = [
        'data' => $results,
    ];
    $response = [
        'agents' => $agents,
        'data_entry_agents' => $data_entry_agents
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage()); // Log error for security
    echo json_encode(['error' => 'A database error occurred.']);
}

// Close the connection
$pdo = null;
