<?php
session_start();
require_once __DIR__ . '/../config.php';

try {
    // Create a PDO connection using the config values
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the action parameter
    $action = $_GET['action'] ?? $_POST['action'] ?? null;

    // Fetch parent countries (Accessible to all)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_countries') {

        $stmt = $pdo->query("SELECT id, name FROM countries");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // Fetch child documents for a specific country (Accessible to all)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_documents' && isset($_GET['parent_id'])) {
        $parent_id = intval($_GET['parent_id']); // Ensure it's an integer
        $stmt = $pdo->prepare("SELECT id, document FROM documents WHERE parent_id = :parent_id");
        $stmt->execute(['parent_id' => $parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // 🚨 **Admin-Only Actions** 🚨
    if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
        http_response_code(403); // Return 403 Forbidden
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }

    // Create a new document (Admin Only)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create_document') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['parent_id'], $data['document']) || empty(trim($data['document']))) {
            echo json_encode(['success' => false, 'error' => 'Invalid input']);
            exit;
        }

        $parent_id = intval($data['parent_id']);
        $document = trim($data['document']);

        try {
            $stmt = $pdo->prepare("INSERT INTO documents (parent_id, document) VALUES (:parent_id, :document)");
            $success = $stmt->execute(['parent_id' => $parent_id, 'document' => $document]);

            echo json_encode(['success' => $success, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Update a document (Admin Only)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_document') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id'], $data['document']) || empty(trim($data['document']))) {
            echo json_encode(['success' => false, 'error' => 'Invalid input']);
            exit;
        }
        $id = intval($data['id']);
        $document = trim($data['document']);
        try {
            $stmt = $pdo->prepare("UPDATE documents SET document = :document WHERE id = :id");
            $success = $stmt->execute(['document' => $document, 'id' => $id]);

            echo json_encode(['success' => $success]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Delete a document (Admin Only)
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $action === 'delete_document') {
        // Read the JSON body
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing ID']);
            exit;
        }

        $id = intval($data['id']);

        // Check if document exists before deleting
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'error' => 'Document not found']);
            exit;
        }

        // Delete document
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = :id LIMIT 1");
        $success = $stmt->execute(['id' => $id]);

        echo json_encode(['success' => $success]);
        exit;
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage()); // Log the error instead of exposing it
    echo json_encode(['error' => 'A database error occurred.']);
}
