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

// Handle GET Request: Fetch Countries
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch']) && $_GET['fetch'] === 'countries') {
    try {
        $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name ASC");
        $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "countries" => $countries]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

// Handle POST Requests: Add, Update, Delete Countries
$requestData = json_decode(file_get_contents("php://input"), true);
$action = $requestData['action'] ?? '';

try {
    if ($action === 'save_country') {
        $name = trim($requestData['name'] ?? '');
        if ($name === '') {
            echo json_encode(["status" => "error", "message" => "Country name cannot be empty!"]);
            exit;
        }

        // Insert country (Update if exists)
        $stmt = $pdo->prepare("INSERT INTO countries (name) VALUES (:name) ON DUPLICATE KEY UPDATE name = :name");
        $stmt->execute([':name' => $name]);
        echo json_encode(["status" => "success", "message" => "Country saved successfully!"]);
    }

    if ($action === 'update_country') {
        $id = $requestData['id'] ?? null;
        $newName = trim($requestData['new_country_name'] ?? '');

        if (!$id || $newName === '') {
            echo json_encode(["status" => "error", "message" => "Country ID and new name are required!"]);
            exit;
        }

        // Update country name
        $stmt = $pdo->prepare("UPDATE countries SET name = :new_country_name WHERE id = :id");
        $stmt->execute([':new_country_name' => $newName, ':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Country updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No changes made or country not found!"]);
        }
    }

    if ($action === 'delete_country') {
        $id = $requestData['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Country ID is required for deletion!"]);
            exit;
        }
        try {
            // Check if any visa application references this country
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) as total
                FROM visa_applications
                WHERE application_country_id = :country_id
            ");
            $checkStmt->execute([':country_id' => $id]); // ✅ Corrected Placeholder

            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                $pdo->rollBack(); // Roll back the transaction
                echo json_encode(["status" => "error", "message" => "Country cannot be deleted! It is linked to visa applications."]);
                exit;
            }
            // Delete all documents where parent_id matches the country id
            $docStmt = $pdo->prepare("DELETE FROM documents WHERE parent_id = :id");
            $docStmt->execute([':id' => $id]);
            // Proceed with country deletion
            $stmt = $pdo->prepare("DELETE FROM countries WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Country and related documents deleted successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Country not found!"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Database error", "error" => $e->getMessage()]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
exit;
