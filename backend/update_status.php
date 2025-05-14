<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;

    if (!$id || !$status) {
        echo json_encode(["success" => false, "error" => "Invalid data"]);
        exit;
    }


    // Fetch advance_payment and total_amount from the database
    $query = "SELECT advance_amount, total_amount FROM visa_applications WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["success" => false, "error" => "Application not found"]);
        exit;
    }

    $advancePayment = $result['advance_amount'];
    $totalAmount = $result['total_amount'];

    // Only allow status change to "Completed" if full payment is made
    if ($status === "Completed" && $advancePayment < $totalAmount) {
        echo json_encode(["success" => false, "message" => "Full payment required for completion"]);
        exit;
    }


    $stmt = $pdo->prepare("UPDATE visa_applications SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status updated to {$status}."]);
    } else {
        echo json_encode(["success" => false,  "message" => "Failed to update status."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error"]);
}
