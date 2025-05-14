<?php
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

try {
    // ✅ Connect to the Database
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Handle CRUD Operations
    $method = $_SERVER['REQUEST_METHOD'];
    $user_id = $_SESSION['id'];

    if ($method === "POST") {  // CREATE Notice
        if (!isset($_POST['noticeText']) || empty(trim($_POST['noticeText']))) {
            echo json_encode(["error" => "Notice text is required."]);
            exit;
        }
        $notice_text = trim($_POST['noticeText']);
        $stmt = $pdo->prepare("INSERT INTO notices (notice_text,user_id) VALUES (:notice_text, :user_id)");
        $stmt->bindParam(':notice_text', $notice_text, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Notice posted successfully!", "id" => $pdo->lastInsertId()]);
        } else {
            echo json_encode(["error" => "Failed to post notice."]);
        }
    } elseif ($method === "GET") { // READ Notices
        $stmt = $pdo->query("select * from notices  inner join users on users.id = notices.user_id ORDER BY created_at DESC");
        $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($notices);
    } elseif ($method === "PUT") { // UPDATE Notice
        parse_str(file_get_contents("php://input"), $_PUT);

        if (!isset($_PUT['id']) || !isset($_PUT['noticeText']) || empty(trim($_PUT['noticeText']))) {
            echo json_encode(["error" => "Invalid request."]);
            exit;
        }

        $id = (int) $_PUT['id'];
        $notice_text = trim($_PUT['noticeText']);

        $stmt = $pdo->prepare("UPDATE notices SET notice_text = :notice_text WHERE id = :id");
        $stmt->bindParam(':notice_text', $notice_text, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Notice updated successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to update notice."]);
        }
    } elseif ($method === "DELETE") { // DELETE Notice
        parse_str(file_get_contents("php://input"), $_DELETE);

        if (!isset($_DELETE['id']) || empty($_DELETE['id'])) {
            echo json_encode(["error" => "Invalid request."]);
            exit;
        }

        $id = (int) $_DELETE['id'];
        $stmt = $pdo->prepare("DELETE FROM notices WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Notice deleted successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to delete notice."]);
        }
    } else {
        echo json_encode(["error" => "Invalid request method."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database Error: " . $e->getMessage()]);
}
