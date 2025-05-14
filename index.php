<?php
# Initialize the session
session_start();
# If user is not logged in then redirect him to login page
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Visa Processing System</title>
  <link href="css/tailwind.css" rel="stylesheet" />
  <link rel="icon" href="src/logo.png" type="image/x-icon">
  <link rel="shortcut icon" href="src/logo.png" type="image/x-icon">
</head>

<body
  style="background-image: url('src/bgpage.png'); background-size: cover; background-position: center; background-repeat: no-repeat;"
  class="flex flex-col items-center justify-center h-screen w-screen m-0 p-0 relative bg-blend-overlay bg-red-100 bg-opacity-50">

  <!-- Navigation Bar -->
  <nav class="flex w-full justify-between gap-10 relative z-10 p-4 sm:p-6">
    <!-- Logo -->
    <div
      class="w-20 h-20 sm:w-32 sm:h-32 bg-cover bg-no-repeat bg-center"
      style="background-image: url('/../src/logo.png')"></div>
    <!-- Links -->
    <div class="flex flex-wrap flex-col sm:flex-row w-full h-fit text-nowrap gap-5 pt-5 items-end justify-end">
      <a href="noticeboard.php" class="cursor-pointer px-5 py-2  bg-red-600 text-white hover:bg-red-800 rounded-xl text-xl leading-none w-fit text-center">
        Notice Board
      </a>
      <a href="visainfomation.php" class="cursor-pointer px-5 py-2  bg-red-600 text-white hover:bg-red-800 rounded-xl text-xl leading-none w-fit text-center">
        Visa Information Center
      </a>
      <?php if ($isLoggedIn): ?>
        <a href="panel.php" class="cursor-pointer px-5 py-2  bg-red-600 text-white hover:bg-red-800 rounded-xl text-xl leading-none w-fit text-center">Dashboard</a>
        <a href="logout.php" class="cursor-pointer px-5 py-2  bg-white text-red-600 hover:bg-red-600 hover:text-white rounded-xl text-xl leading-none w-fit text-center">Logout</a>
      <?php else: ?>
        <a href="login.php" class="cursor-pointer px-5 py-2  bg-red-600 text-white hover:bg-red-800 rounded-xl text-xl leading-none w-fit text-center">Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Main Content -->
  <div
    class="flex w-full h-full flex-col gap-5 text-6xl pt-10 items-center flex-wrap text-wrap">
    <!-- Text Content -->
    <p
      class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-bold text-black pt-10 sm:pt-4">
      Visa Processing
    </p>
    <p
      class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-bold text-red-500 pt-2 sm:pt-4">
      System
    </p>
    <img src="src/indeximage.png" alt="" class="w-32 sm:w-64 pt-10">

  </div>
</body>

</html>