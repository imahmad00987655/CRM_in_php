<?php
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]));
}

// Handle GET Request: Fetch Cities
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch']) && $_GET['fetch'] === 'cities') {
    try {
        $stmt = $pdo->query("SELECT id, city_name FROM cities ORDER BY city_name ASC");
        $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "cities" => $cities]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

// Handle POST Requests: Add, Update, Delete Cities
$requestData = json_decode(file_get_contents("php://input"), true);
$action = $requestData['action'] ?? '';

try {
    if ($action === 'save_city') {
        $city_name = trim($requestData['city_name'] ?? '');
        if ($city_name === '') {
            echo json_encode(["status" => "error", "message" => "City name cannot be empty!"]);
            exit;
        }

        // Insert city
        $stmt = $pdo->prepare("INSERT INTO cities (city_name) VALUES (:city_name) ON DUPLICATE KEY UPDATE city_name = :city_name");
        $stmt->execute([':city_name' => $city_name]);
        echo json_encode(["status" => "success", "message" => "City saved successfully!"]);
    }

    if ($action === 'update_city') {
        $id = $requestData['id'] ?? null;
        $newName = trim($requestData['new_city_name'] ?? '');

        if (!$id || $newName === '') {
            echo json_encode(["status" => "error", "message" => "City ID and new name are required!"]);
            exit;
        }

        // Update city name
        $stmt = $pdo->prepare("UPDATE cities SET city_name = :new_city_name WHERE id = :id");
        $stmt->execute([':new_city_name' => $newName, ':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "City updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No changes made or city not found!"]);
        }
    }

    if ($action === 'delete_city') {
        $id = $requestData['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "City ID is required for deletion!"]);
            exit;
        }
        try {
            // Check if any user from this city has an application
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) as total
                FROM visa_applications va
                JOIN users u ON va.visa_agent = u.id OR va.data_entry_agent = u.id
                WHERE u.city = :city
            ");
            $checkStmt->execute([':city' => $id]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                echo json_encode(["status" => "error", "message" => "City cannot be deleted! A user from this city is assigned to visa applications."]);
                exit;
            }

            // Proceed with city deletion if no users have applications
            $stmt = $pdo->prepare("DELETE FROM cities WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "City deleted successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "City not found!"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Database error", "error" => $e->getMessage()]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
exit;
