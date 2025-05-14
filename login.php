<?php
# Initialize session
session_start();

# Include connection
require_once "config.php";

# Define variables and initialize with empty values
$user_login_err = $user_password_err = $login_err = "";
$user_login = $user_password = "";

# Generate CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

# Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  //   die("Invalid CSRF token.");
  // }

  if (empty(trim($_POST["user_login"]))) {
    $user_login_err = "Please enter your username or an email id.";
  } else {
    $user_login = trim($_POST["user_login"]);
  }

  if (empty(trim($_POST["user_password"]))) {
    $user_password_err = "Please enter your password.";
  } else {
    $user_password = trim($_POST["user_password"]);
  }

  # Validate credentials 
  if (empty($user_login_err) && empty($user_password_err)) {
    $sql = "SELECT users.id, users.username, users.password, users.user_role, 
               users.name, users.email, users.phone_number, users.gender, 
               users.date_of_birth, users.cnic, users.city, user_roles.role_name 
        FROM users 
        LEFT JOIN user_roles ON users.user_role = user_roles.id
        WHERE users.username = ? OR users.email = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "ss", $param_user_login, $param_user_login);
      $param_user_login = $user_login;

      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result(
            $stmt,
            $id,
            $username,
            $hashed_password,
            $user_role,
            $name,
            $email,
            $phone_number,
            $gender,
            $date_of_birth,
            $cnic,
            $city,
            $role_name
          );
          if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($user_password, $hashed_password)) {
              # SUCCESSFUL LOGIN - TRACK ATTEMPTS
              track_login_attempt($id, $link);
              $_SESSION["id"] = $id;
              $_SESSION["email"] = $email;
              $_SESSION["phone_number"] = $phone_number ?? '';
              $_SESSION["gender"] = $gender ?? '';
              $_SESSION["date_of_birth"] = $date_of_birth ?? '';
              $_SESSION["loggedin"] = TRUE;
              $_SESSION["username"] = $username ?? '';
              // $_SESSION["role_name"] = $role_name;
              $_SESSION["name"] = $name;
              $_SESSION["cnic"] = $cnic;
              $_SESSION["city"] = $city;
              $_SESSION["user_role"] = $role_name; # Store role name in session
              header("Location: panel.php");
              exit;
            } else {
              # SUCCESSFUL LOGIN - TRACK ATTEMPTS
              track_login_attempt($id, $link);
              $login_err = "The email or password you entered is incorrect.";
            }
          }
        } else {
          # If user does not exist, create a new login attempt record
          $login_err = "Invalid username or password.";
        }
      } else {
        echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        echo "<script>window.location.href='login.php';</script>";
        exit;
      }
      mysqli_stmt_close($stmt);
    }
  }
  mysqli_close($link);
}

/**
 * Function to track login attempts (Creates or Updates)
 * @param int|null $user_id - The user ID if found, otherwise null
 * @param object $link - Database connection
 * @param string|null $user_login - The login identifier (for non-existing users)
 */
function track_login_attempt($user_id, $link, $user_login = null)
{
  error_log("Tracking login attempt for User ID: " . ($user_id ?? 'Unknown'));

  if (!$user_id) {
    # If user ID is null, try to get the user ID from username/email
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "ss", $user_login, $user_login);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);

      if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $user_id);
        mysqli_stmt_fetch($stmt);
      }
      mysqli_stmt_close($stmt);
    }
  }

  if ($user_id) {
    # Check if the user already has a login attempt record
    $sql = "SELECT attempts FROM login_attempts WHERE user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);

      if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $attempts);
        mysqli_stmt_fetch($stmt);
        $attempts++;

        $update_sql = "UPDATE login_attempts SET attempts = ?, last_attempt = NOW() WHERE user_id = ?";
        if ($update_stmt = mysqli_prepare($link, $update_sql)) {
          mysqli_stmt_bind_param($update_stmt, "ii", $attempts, $user_id);
          mysqli_stmt_execute($update_stmt);
          mysqli_stmt_close($update_stmt);
          error_log("Updated login attempts for User ID: $user_id to $attempts");
        }
      } else {
        $insert_sql = "INSERT INTO login_attempts (user_id, attempts, last_attempt) VALUES (?, 1, NOW())";
        if ($insert_stmt = mysqli_prepare($link, $insert_sql)) {
          mysqli_stmt_bind_param($insert_stmt, "i", $user_id);
          mysqli_stmt_execute($insert_stmt);
          mysqli_stmt_close($insert_stmt);
          error_log("Inserted new login attempt record for User ID: $user_id");
        }
      }
      mysqli_stmt_close($stmt);
    }
  } else {
    error_log("Login attempt tracked but no matching user found.");
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visa Processing System</title>
  <link href="./css/tailwind.css" rel="stylesheet" />
  <link rel="icon" href="src/logo.png" type="image/x-icon">
  <link rel="shortcut icon" href="src/logo.png" type="image/x-icon">
</head>

<body class="h-screen w-screen relative flex flex-col items-center justify-center"
  style="background-image: url('src/bgpage.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
  <div class="absolute inset-0 bg-white bg-opacity-50 z-10"></div>
  <nav class="absolute top-10 left-10 justify-between items-center z-20 ">
    <div onclick="window.location.href='/';" class="w-20 h-20 sm:w-32 sm:h-32 bg-cover bg-no-repeat bg-center cursor-pointer"
      style="background-image: url('src/logo.png')"></div>
  </nav>

  <div class="flex flex-col gap-10 max-w-md p-10 bg-white rounded-xl shadow-md relative z-20">
    <p class="text-center text-xl font-semibold">Welcome, Log into your account</p>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="w-full flex flex-col gap-2">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <input type="text" name="user_login" placeholder="Enter your email or username"
        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="<?= $user_login; ?>" required>
      <small class="text-danger"><?= $user_login_err; ?></small>
      <input type="password" name="user_password" placeholder="Enter your password"
        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        required>
      <small class="text-danger"><?= $user_password_err; ?></small>
      <button type="submit"
        class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-300 ease-in-out">Log
        in</button>
    </form>
    <p class="text-center text-sm text-gray-800">Unlock your world - Secure, Simple, Seamless Access</p>
    <a href="/" class="cursor-pointer hover:bg-red-600 hover:text-white py-2 bg-white text-black border-b-2 border-black text-lg leading-none w-16 text-center">Back</a>
  </div>

</body>

</html>