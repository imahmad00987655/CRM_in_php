<?php
// Database Configuration
define("DB_SERVER", "localhost");
define("DB_USERNAME", "root");
// define("DB_PASSWORD", "123");
define("DB_PASSWORD", ""); //todo revert this
define("DB_NAME", "vpsdb");

// Create Connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check Connection
if (!$link) {
  die("Database connection failed: " . mysqli_connect_error());
}
