<?php
# Initialize the session
session_start();

# Unset all session variables
$_SESSION = array();

# Destroy the session
session_destroy();
echo "<script>
        localStorage.clear();
        sessionStorage.clear();
        window.location.href = 'login.php';
      </script>";


# Redirect to login page
echo "<script>" . "window.location.href='login.php';" . "</script>";
exit;
