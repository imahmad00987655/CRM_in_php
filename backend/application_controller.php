<?php
session_start();
require_once __DIR__ . '/../config.php';

header("Content-Type: application/json");
$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $userRole   = $_SESSION["user_role"];
        $username   = $_SESSION["name"];
        $userId     = $_SESSION["id"];
        $userCityId = $_SESSION["city"];

        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

        // Base queries
        $query = "SELECT v.id, v.applicant_name,v.applicant_surname, v.applicant_cnic, v.application_country_id, v.occupation, v.persons, v.person_1, v.person_2, v.person_3, v.person_4,
                         v.applicant_address, v.phone_number, v.passport_number, v.traveling_plan, v.extra_info, v.proceed_to_agent,
                         c.name AS application_country, v.application_city, ct.city_name AS application_city_name, v.status, 
                         v.advance_amount, v.total_amount, DATE_FORMAT(v.created_at, '%D %M %Y') AS created_at,
                         a.name AS visa_agent_name, d.name AS data_entry_agent_name, v.visa_agent, v.data_entry_agent,v.application_limit,DATE_FORMAT(v.deadline_date, '%D %M %Y') AS deadline_date_parsed, v.deadline_date
                  FROM visa_applications v
                  LEFT JOIN countries c ON v.application_country_id = c.id
                  LEFT JOIN cities ct ON v.application_city = ct.id
                  LEFT JOIN users a ON v.visa_agent = a.id
                  LEFT JOIN users d ON v.data_entry_agent = d.id
                  WHERE 1=1";

        $countQuery = "SELECT COUNT(*) FROM visa_applications WHERE 1=1";
        $sumQuery = "SELECT 
                        SUM(advance_amount) AS total_advance, 
                        SUM(total_amount - advance_amount) AS total_balance 
                     FROM visa_applications WHERE 1=1";

        // Apply filters
        if (!empty($search)) {
            $query .= " AND (LOWER(v.applicant_name) LIKE LOWER(:search) 
                        OR LOWER(v.applicant_cnic) LIKE LOWER(:search))";
            $countQuery .= " AND (LOWER(applicant_name) LIKE LOWER(:search) 
                            OR LOWER(applicant_cnic) LIKE LOWER(:search))";
            $sumQuery .= " AND (LOWER(applicant_name) LIKE LOWER(:search) 
                          OR LOWER(applicant_cnic) LIKE LOWER(:search))";
        }

        if (!empty($status)) {
            $query .= " AND v.status = :status";
            $countQuery .= " AND status = :status";
            $sumQuery .= " AND status = :status";
        }

        if (!empty($start_date)) {
            $query .= " AND DATE(v.created_at) >= :start_date";
            $countQuery .= " AND DATE(created_at) >= :start_date";
            $sumQuery .= " AND DATE(created_at) >= :start_date";
        }

        if (!empty($end_date)) {
            $query .= " AND DATE(v.created_at) <= :end_date";
            $countQuery .= " AND DATE(created_at) <= :end_date";
            $sumQuery .= " AND DATE(created_at) <= :end_date";
        }

        if ($userRole === 'data_entry_agent') {
            $query .= " AND v.data_entry_agent = :userId AND v.status != 'Completed'";
            $countQuery .= " AND data_entry_agent = :userId AND status != 'Completed'";
            $sumQuery .= " AND data_entry_agent = :userId AND status != 'Completed'";
        } elseif ($userRole === 'visa_agent') {
            $query .= " AND v.visa_agent = :userId AND v.proceed_to_agent = 1 AND v.status != 'Completed'";
            $countQuery .= " AND visa_agent = :userId AND proceed_to_agent = 1 AND status != 'Completed'";
            $sumQuery .= " AND visa_agent = :userId AND proceed_to_agent = 1 AND status != 'Completed'";
        } elseif ($userRole === 'manager') {
            $query .= " AND v.application_city = $userCityId  ";
            $countQuery .= " AND application_city = $userCityId  ";
            $sumQuery .= " AND application_city = $userCityId  ";
        }

        $query .= " ORDER BY v.created_at DESC LIMIT :limit OFFSET :offset";

        // Prepare statements
        $stmt = $pdo->prepare($query);
        $countStmt = $pdo->prepare($countQuery);
        $sumStmt = $pdo->prepare($sumQuery);

        // Bind parameters
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $sumStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        if (!empty($status)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $countStmt->bindValue(':status', $status, PDO::PARAM_STR);
            $sumStmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        if (!empty($start_date)) {
            $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
            $countStmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
            $sumStmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
        }
        if (!empty($end_date)) {
            $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
            $countStmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
            $sumStmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
        }

        if ($userRole === 'data_entry_agent' || $userRole === 'visa_agent' ) {
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $countStmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $sumStmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Execute queries
        $stmt->execute();
        $countStmt->execute();
        $sumStmt->execute();

        // Fetch sum values
        $sumResult = $sumStmt->fetch(PDO::FETCH_ASSOC);
        $totalAdvance = $sumResult['total_advance'] ?? 0;
        $totalBalance = $sumResult['total_balance'] ?? 0;

        echo json_encode([
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC),
            "total" => $countStmt->fetchColumn(),
            "total_advance" => $totalAdvance,
            "total_balance" => $totalBalance,
            "role" => $userRole
        ]);
    } catch (PDOException $e) {
        die($e);
        echo json_encode(["error" => "Database error", "message" => $e->getMessage()]);
    }
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data["id"])) {
        echo json_encode(["error" => "No application ID provided"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT status FROM visa_applications WHERE id = :id");
        $stmt->bindParam(":id", $data["id"], PDO::PARAM_INT);
        $stmt->execute();
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            echo json_encode(["success" => false, "message" => "Application not found"]);
            exit;
        }

        if ($application["status"] === "Completed") {
            echo json_encode(["success" => false, "message" => "Completed application cannot be deleted"]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM visa_applications WHERE id = :id");
        $stmt->bindParam(":id", $data["id"], PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, 'message' => "Application deleted successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error", "message" => $e->getMessage()]);
    }
} elseif ($method === 'POST' || $method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $isUpdate = ($method === 'PUT');

    $fields = [
        "application_country_id",
        "application_city",
        "occupation",
        "persons",
        "visa_agent",
        "data_entry_agent",
        "applicant_name",
        "applicant_surname",
        "phone_number",
        "total_amount",
        "advance_amount",
        "applicant_cnic",
        "passport_number",
        "proceed_to_agent",
        "applicant_address",
        "extra_info",
        "traveling_plan",
        "status",
        "person_1",
        "person_2",
        "person_3",
        "person_4",
//        "application_limit",
        "deadline_date"
    ];
    $data['proceed_to_agent'] = filter_var($data['proceed_to_agent'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

    if(!empty($data['proceed_to_agent']) && !empty($data['status']) && $data['status'] == 'Pending'){
        $data['status'] = 'In Process';
    }

    // Ensure empty arrays are set to NULL
    foreach ($data as $key => $value) {
        if (is_array($value) && empty($value)) {
            $data[$key] = null;
        }
    }
    $numericFields = ["application_country_id", "application_limit", "application_city", "total_amount", "advance_amount", 'visa_agent', 'data_entry_agent'];
    foreach ($numericFields as $field) {
        if (isset($data[$field]) && !is_numeric($data[$field])) {
            echo json_encode(["error" => "$field must be a number."]);
            exit;
        }
//        if(isset($data[$field]) && $field == 'advance_amount' && $data[$field] > $data['total_amount'] ){
//            echo json_encode(["error" => "$field must be less then total amount."]);
//            exit;
//        }
    }
    if ($isUpdate) {
        $setFields = implode(", ", array_map(fn($field) => "$field = :$field", $fields));
        $query = "UPDATE visa_applications SET $setFields WHERE id = :id";
    } else {
        $query = "INSERT INTO visa_applications (" . implode(", ", $fields) . ") VALUES (" .
            implode(", ", array_map(fn($field) => ":$field", $fields)) . ")";
    }

    try {
        $stmt = $pdo->prepare($query);
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $value = is_array($data[$field]) ? json_encode($data[$field]) : $data[$field];
                $stmt->bindValue(":$field", $value, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(":$field", null, PDO::PARAM_NULL);
            }
        }
        if ($isUpdate) {
            $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
        }

        $stmt->execute();
        echo json_encode(["message" => $isUpdate ? "Application updated" : "Application assigned for data entry.", "id" => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error", "message" => $e->getMessage()]);
    }
}
