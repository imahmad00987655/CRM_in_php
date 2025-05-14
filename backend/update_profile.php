<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!$_SESSION["loggedin"]) {
    echo json_encode(["status" => "error", "message" => "User not logged in!"]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]));
}


// Fetch user ID from session
$userId = $_SESSION['id'];
$requestData = json_decode(file_get_contents("php://input"), true);

// Get form inputs
$email = $requestData['email'] ?? '';
$username = $requestData['username'] ?? '';
$user_role = $requestData['user_role'] ?? '';
$name = $requestData['name'] ?? '';
$gender = $requestData['gender'] ?? '';
$city = $requestData['city'] ?? '';
$phone_number = $requestData['phone_number'] ?? '';
$cnic = $requestData['cnic'] ?? '';
$date_of_birth = !empty($requestData['date_of_birth']) ? $requestData['date_of_birth'] : null;
$password = $requestData['password'] ?? '';

// Validate required fields
if (empty($name) || empty($email) || empty($phone_number)) {
    echo json_encode(["status" => "error", "message" => "Name, email, and phone number are required!"]);
    exit;
}

// Update user details
try {
    $stmt = $pdo->prepare("UPDATE users 
        SET name = :name, email = :email, username = :username, user_role = :user_role, 
            gender = :gender, city = :city, phone_number = :phone_number, cnic = :cnic, date_of_birth = :date_of_birth 
        WHERE id = :id");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':username' => $username,
        ':user_role' => $user_role,
        ':gender' => $gender,
        ':city' => $city,
        ':phone_number' => $phone_number,
        ':cnic' => $cnic,
        ':date_of_birth' => $date_of_birth,
        ':id' => $userId
    ]);

    // If the user provided a new password, update it securely
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

    // Update session data
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['user_role'] = $user_role;
    $_SESSION['user']['gender'] = $gender;
    $_SESSION['user']['city'] = $city;
    $_SESSION['user']['phone_number'] = $phone_number;
    $_SESSION['user']['cnic'] = $cnic;
    $_SESSION['user']['date_of_birth'] = $date_of_birth;

    echo json_encode(["status" => "success", "message" => "Profile updated successfully!", "user" => $_SESSION['user']]);
    session_unset();
    session_destroy();
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Failed to update profile: " . $e->getMessage()]);
}
