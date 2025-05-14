<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $method = $_SERVER['REQUEST_METHOD'];
    $userRole = $_SESSION["user_role"];
    $userCity = $_SESSION["city"] ?? 0;

    switch ($method) {
        case 'GET': // Read Users (Paginated)
            // Fetch Cities
            if (isset($_GET['fetch']) && $_GET['fetch'] === 'cities') {
                try {
                    $query = "SELECT id,city_name FROM cities"; // Default query

                    if ($userRole == 'manager') {
                        $query .= " WHERE id = $userCity";
                    }
                    $stmt = $pdo->query($query);
                    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                } catch (PDOException $e) {
                    echo json_encode(["status" => "error", "message" => "Failed to fetch cities."]);
                }
                exit;
            }
            // Fetch Roles
            if (isset($_GET['fetch']) && $_GET['fetch'] === 'roles') {
                try {
                    $query = "SELECT id, role_name FROM user_roles"; // Default query

                    if ($userRole == 'manager') {
                        $query .= " WHERE role_name NOT IN ('admin','manager')";
                    }
                    if ($userRole == 'visa_agent') {
                        $query .= " WHERE role_name NOT IN ('admin','manager')";
                    }

                    $stmt = $pdo->query($query);
                    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                } catch (PDOException $e) {
                    echo json_encode(["status" => "error", "message" => "Failed to fetch roles."]);
                }
                exit;
            }

            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($page - 1) * $limit;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter = isset($_GET['user_role']) ? trim($_GET['user_role']) : '';

            $query = "SELECT users.id, users.name, users.email, users.phone_number, users.cnic, users.date_of_birth, 
            users.username, users.gender, users.user_role AS role_id, 
            user_roles.role_name, cities.city_name AS city, users.city AS city_id
            FROM users 
            JOIN user_roles ON users.user_role = user_roles.id
            LEFT JOIN cities ON users.city = cities.id
            WHERE users.user_role IN (SELECT id FROM user_roles)";

            $countQuery = "SELECT COUNT(*) 
          FROM users 
          JOIN user_roles ON users.user_role = user_roles.id
          LEFT JOIN cities ON users.city = cities.id
          WHERE users.user_role IN (SELECT id FROM user_roles)";

            if ($userRole == 'manager') {
                $query .= " AND user_roles.role_name NOT IN ('admin','manager')
                 AND (
                 (user_roles.role_name IN ('data_entry_agent', 'visa_agent') AND users.city = $userCity)
                 OR user_roles.role_name NOT IN ('data_entry_agent', 'visa_agent')
                 )";
                $countQuery .= " AND user_roles.role_name NOT IN ('admin','manager')
                AND (
                 (user_roles.role_name IN ('data_entry_agent', 'visa_agent') AND users.city = $userCity)
                 OR user_roles.role_name NOT IN ('data_entry_agent', 'visa_agent')
                 )";
            }


            if (!empty($filter)) {
                $query .= " AND user_role = :user_role";
                $countQuery .= " AND user_role = :user_role";
            }

            if (!empty($search)) {
                $query .= " AND (LOWER(name) LIKE LOWER(:search) 
                                OR LOWER(city_name) LIKE LOWER(:search) 
                                OR role_name LIKE :search 
                                OR cnic LIKE :search)";
                $countQuery .= " AND (LOWER(name) LIKE LOWER(:search) 
                                      OR LOWER(city_name) LIKE LOWER(:search) 
                                      OR role_name LIKE :search 
                                      OR cnic LIKE :search)";
            }

            $query .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($query);
            $countStmt = $pdo->prepare($countQuery);

            if (!empty($search)) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
                $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }
            if (!empty($filter)) {
                $stmt->bindValue(':user_role', $filter, PDO::PARAM_STR);
                $countStmt->bindValue(':user_role', $filter, PDO::PARAM_STR);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $countStmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $total = $countStmt->fetchColumn();

            foreach ($users as &$user) {
                $officerId = $user['id'];

                // Optimized SQL query
                $appQuery = "SELECT 
                            COUNT(*) AS total, 
                            COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS completed,
                            COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS pending,
                            COUNT(CASE WHEN status = 'Cancelled' THEN 1 END) AS cancelled,
                            COUNT(CASE WHEN status = 'In Process' THEN 1 END) AS in_process
                            FROM visa_applications 
                            WHERE visa_agent = :officerId OR data_entry_agent = :officerId";


                $appStmt = $pdo->prepare($appQuery);
                $appStmt->bindValue(':officerId', $officerId, PDO::PARAM_INT);
                $appStmt->execute();
                $appCounts = $appStmt->fetch(PDO::FETCH_ASSOC);

                $user['total'] = $appCounts['total'] ?? 0;
                $user['completed'] = $appCounts['completed'] ?? 0;
                $user['pending'] = $appCounts['pending'] ?? 0;
                $user['cancelled'] = $appCounts['cancelled'] ?? 0;
                $user['in_process'] = $appCounts['in_process'] ?? 0;
            }

            echo json_encode(['data' => $users, 'total' => $total]);
            break;

        case 'POST': // Create User
            $input = json_decode(file_get_contents("php://input"), true);
            if (!isset($input['name'], $input['email'], $input['user_role'], $input['password'])) {
                echo json_encode(["error" => "Missing required fields"]);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO users (name, email, city, phone_number, cnic, date_of_birth, gender, user_role, username, password) 
                                   VALUES (:name, :email, :city, :phone_number, :cnic, :date_of_birth, :gender, :user_role, :username, :password)");
            $stmt->execute([
                ':name' => $input['name'],
                ':email' => $input['email'],
                ':city' => $input['city'] ?? null,
                ':phone_number' => $input['phone_number'] ?? null,
                ':cnic' => $input['cnic'] ?? null,
                ':date_of_birth' => $input['date_of_birth'] ?? null,
                ':gender' => $input['gender'] ?? null,
                ':user_role' => $input['user_role'],
                ':username' => $input['username'],
                ':password' => password_hash($input['password'], PASSWORD_DEFAULT)
            ]);

            echo json_encode(["message" => "User created successfully"]);
            break;

        case 'PUT': // Update User
            $input = json_decode(file_get_contents("php://input"), true);
            if (!isset($input['id'])) {
                echo json_encode(["error" => "Missing user ID"]);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, city = :city, phone_number = :phone_number, 
                                   cnic = :cnic, date_of_birth = :date_of_birth, user_role = :user_role ,username= :username, gender = :gender
                                   WHERE id = :id");
            $stmt->execute([
                ':id' => $input['id'],
                ':name' => $input['name'] ?? null,
                ':email' => $input['email'] ?? null,
                ':city' => $input['city'] ?? null,
                ':phone_number' => $input['phone_number'] ?? null,
                ':cnic' => $input['cnic'] ?? null,
                ':date_of_birth' => $input['date_of_birth'] ?? null,
                ':user_role' => $input['user_role'] ?? null,
                ':username' => $input['username'],
                ':gender' => $input['gender'],
            ]);
            if (!empty($input['password'])) {
                $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->execute([
                    ':password' => $hashedPassword,
                    ':id' => $input['id']
                ]);
            }

            $response = ["message" => "User updated successfully"];
            // ✅ Always return a response
            echo json_encode($response);
            exit;
            break;
        case 'DELETE': // Delete user
            parse_str(file_get_contents("php://input"), $data);
            if (!isset($_GET['id'])) {
                echo json_encode(["success" => false, "message" => "Missing user ID"]);
                exit;
            }
            $userId = $_GET['id'];

            try {
                // Check if the user is assigned to any visa applications
                $checkStmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM visa_applications
            WHERE visa_agent = :user_id OR data_entry_agent = :user_id
        ");
                $checkStmt->execute([':user_id' => $userId]);
                $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($result['total'] > 0) {
                    echo json_encode(["success" => false, "message" => "User cannot be deleted! They are assigned to visa applications."]);
                    exit;
                }

                // Proceed with user deletion
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);

                if ($stmt->rowCount() > 0) {
                    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
                } else {
                    echo json_encode(["success" => false, "message" => "User not found!"]);
                }
            } catch (PDOException $e) {
                echo json_encode(["success" => false, "message" => "Database error", "error" => $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(["error" => "Invalid request method"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error", "message" => $e->getMessage()]);
}
