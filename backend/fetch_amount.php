<?php
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$country_id = isset($_GET['country_id']) ? intval($_GET['country_id']) : 0;
$persons = isset($_GET['persons']) ? trim($_GET['persons']) : '';

if ($country_id === 0 || empty($persons)) {
    echo json_encode(['error' => 'Country and persons parameters are required.']);
    exit;
}

try {
    // ✅ Enable MySQLi Exception Mode
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // ✅ Establish MySQLi Connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // ✅ Mapping frontend values to correct DB columns
    $person_mapping = [
        'single_person' => 'single_person',
        'couple' => 'couple',
        'family-3' => 'family_3',
        'family-4' => 'family_4'
    ];

    if (!isset($person_mapping[$persons])) {
        throw new Exception('Invalid selection.');
    }

    $column_name = $person_mapping[$persons];

    // ✅ Prepare and execute the query
    $query = "SELECT `$column_name` AS amount FROM visa_charges WHERE country_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $country_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ Fetch result
    $row = $result->fetch_assoc();
    if ($row) {
        echo json_encode(['amount' => $row['amount']]);
    } else {
        throw new Exception('No data found.');
    }

    // ✅ Close connections
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
