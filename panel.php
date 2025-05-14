<?php
# Initialize the session
session_start();
# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
  header("Location: login.php");
  exit;
}
$titlePrefix = isset($_SESSION['user_role']) ? ucwords($_SESSION['user_role']) : "";
$loggedUserRole = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php echo $titlePrefix; ?> Panel</title>
  <link rel="icon" href="src/logo.png" type="image/x-icon">
  <link rel="shortcut icon" href="src/logo.png" type="image/x-icon">
  <link href="./css/tailwind.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
  <!-- Main container -->
  <div class="flex flex-col lg:flex-row fixed w-full h-full">

    <!-- Sidebar -->
    <div id="sideBar" class="w-full hidden sm:w-[35%] lg:w-[20%] bg-red-600 text-white lg:flex flex-col gap-10 p-4">
      <!-- Profile section -->
      <div class="flex flex-col gap-10">
        <div onclick="backToIndex()" class="flex w-fit h-fit items-center cursor-pointer justify-between ">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-10 h-auto fill-current">
            <path d="M96 128a128 128 0 1 0 256 0A128 128 0 1 0 96 128zm94.5 200.2l18.6 31L175.8 483.1l-36-146.9c-2-8.1-9.8-13.4-17.9-11.3C51.9 342.4 0 405.8 0 481.3c0 17 13.8 30.7 30.7 30.7l131.7 0c0 0 0 0 .1 0l5.5 0 112 0 5.5 0c0 0 0 0 .1 0l131.7 0c17 0 30.7-13.8 30.7-30.7c0-75.5-51.9-138.9-121.9-156.4c-8.1-2-15.9 3.3-17.9 11.3l-36 146.9L238.9 359.2l18.6-31c6.4-10.7-1.3-24.2-13.7-24.2L224 304l-19.7 0c-12.4 0-20.1 13.6-13.7 24.2z" />
          </svg>
        </div>

        <div class=" w-full h-fit bg-red-700 rounded-xl p-3">
          <h4 class="text-lg">Hello, <?= htmlspecialchars($_SESSION["username"]); ?></h4>
          Welcome ! You are now signed in to your account.
        </div>

      </div>
      <!-- Sidebar Links -->
      <div class="flex flex-col w-full h-full gap-4">

      <?php if ( !in_array($loggedUserRole , ['sales_agent'])) : ?>
        <div onclick="loadComponent('dashboard.php','Dashboard')" class="cursor-pointer flex items-center text-white text-lg border border-white hover:bg-red-800 rounded-xl px-3 py-2 gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="w-5 h-auto fill-current">
            <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
          </svg>
          Dashboard
        </div>
      <?php endif; ?>
        <div onclick="loadComponent('applications.php','Applications')" class="cursor-pointer flex items-center text-white text-lg border border-white hover:bg-red-800 rounded-xl px-3 py-2 gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-5 h-auto fill-current">
            <path d="M40 48C26.7 48 16 58.7 16 72l0 48c0 13.3 10.7 24 24 24l48 0c13.3 0 24-10.7 24-24l0-48c0-13.3-10.7-24-24-24L40 48zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32l288 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L192 64zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32l288 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-288 0zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32l288 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-288 0zM16 232l0 48c0 13.3 10.7 24 24 24l48 0c13.3 0 24-10.7 24-24l0-48c0-13.3-10.7-24-24-24l-48 0c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24l0 48c0 13.3 10.7 24 24 24l48 0c13.3 0 24-10.7 24-24l0-48c0-13.3-10.7-24-24-24l-48 0z" />
          </svg>
          Applications
        </div>
        <?php if ( in_array($loggedUserRole , ['admin','manager'])) : ?>
          <div onclick="loadComponent('officers.php', 'Officers')" class="cursor-pointer flex items-center text-white text-lg border border-white hover:bg-red-800 rounded-xl px-3 py-2 gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="w-5 h-auto fill-current">
              <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
            Officers
          </div>
          <div onclick="loadComponent('billing.php', 'Billing')" class="cursor-pointer flex items-center text-white text-lg border border-white hover:bg-red-800 rounded-xl px-3 py-2 gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-5 h-auto fill-current">
              <path d="M320 96L192 96 144.6 24.9C137.5 14.2 145.1 0 157.9 0L354.1 0c12.8 0 20.4 14.2 13.3 24.9L320 96zM192 128l128 0c3.8 2.5 8.1 5.3 13 8.4C389.7 172.7 512 250.9 512 416c0 53-43 96-96 96L96 512c-53 0-96-43-96-96C0 250.9 122.3 172.7 179 136.4c0 0 0 0 0 0s0 0 0 0c4.8-3.1 9.2-5.9 13-8.4zm84 88c0-11-9-20-20-20s-20 9-20 20l0 14c-7.6 1.7-15.2 4.4-22.2 8.5c-13.9 8.3-25.9 22.8-25.8 43.9c.1 20.3 12 33.1 24.7 40.7c11 6.6 24.7 10.8 35.6 14l1.7 .5c12.6 3.8 21.8 6.8 28 10.7c5.1 3.2 5.8 5.4 5.9 8.2c.1 5-1.8 8-5.9 10.5c-5 3.1-12.9 5-21.4 4.7c-11.1-.4-21.5-3.9-35.1-8.5c-2.3-.8-4.7-1.6-7.2-2.4c-10.5-3.5-21.8 2.2-25.3 12.6s2.2 21.8 12.6 25.3c1.9 .6 4 1.3 6.1 2.1c0 0 0 0 0 0s0 0 0 0c8.3 2.9 17.9 6.2 28.2 8.4l0 14.6c0 11 9 20 20 20s20-9 20-20l0-13.8c8-1.7 16-4.5 23.2-9c14.3-8.9 25.1-24.1 24.8-45c-.3-20.3-11.7-33.4-24.6-41.6c-11.5-7.2-25.9-11.6-37.1-15c0 0 0 0 0 0l-.7-.2c-12.8-3.9-21.9-6.7-28.3-10.5c-5.2-3.1-5.3-4.9-5.3-6.7c0-3.7 1.4-6.5 6.2-9.3c5.4-3.2 13.6-5.1 21.5-5c9.6 .1 20.2 2.2 31.2 5.2c10.7 2.8 21.6-3.5 24.5-14.2s-3.5-21.6-14.2-24.5c-6.5-1.7-13.7-3.4-21.1-4.7l0-13.9z" />
            </svg>
            Billing
          </div>
        <?php endif; ?>
        <div onclick="loadComponent('settings.php', 'Settings')" class="cursor-pointer flex items-center text-white text-lg border border-white hover:bg-red-800 rounded-xl px-3 py-2 gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-5 h-auto fill-current">
            <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z" />
          </svg>
          Settings
        </div>
      </div>
      <a href="../logout.php" class="px-5 py-3 rounded-xl h-fit cursor-pointer text-lg leading-none w-full text-center bg-white text-red-600 hover:bg-red-800 hover:text-white">Log out</a>
    </div>

    <!-- Main Content -->
    <div class="bg-white flex flex-col justify-between w-full h-full lg:w-[80%]">
      <!-- Top section -->
      <div class="flex items-center border-b-2 shadow-md relative w-full min-h-16 max-h-[8%]">
        <div id="toggleBtn"
          class="flex lg:hidden items-center justify-center w-10 h-10 ml-3 p-2 bg-red-600 rounded-lg hover:bg-red-700 text-white cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="color:currentColor" class="w-6 h-6 fill-current">
            <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM64 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L96 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z" />
          </svg>
        </div>
        <div class="flex items-center gap-10 ml-auto">
          <div
            class="w-20 h-20 bg-cover bg-no-repeat bg-center"
            style="background-image: url('src/logo.png')"></div>
        </div>
        <h1 id="topBarTitle"
          class="capitalize absolute left-1/2 transform -translate-x-1/2 text-3xl font-semibold text-gray-800">
        </h1>
      </div>

      <!-- Background Image Container -->
      <div
        style="background-image: url('src/bgpage.png'); background-size: cover; background-position: center; background-repeat: no-repeat;"
        id="dashboard-content"
        class="flex flex-col relative w-full h-[92%] bg-blend-overlay bg-red-100 bg-opacity-50">
      </div>
    </div>
  </div>
  <!-- Chart.js Script -->
  <script>
    var titlePrefix = "<?php echo $titlePrefix; ?>"; // Pass PHP variable to JS
    var userRole = "<?php echo $loggedUserRole; ?>";
    document.getElementById('toggleBtn').addEventListener('click', toggleSideBar);

    function toggleSideBar() {
      const sidebar = document.getElementById('sideBar');
      sidebar.classList.toggle('hidden');
      sidebar.classList.add('absolute');
      sidebar.classList.add('flex');
      sidebar.classList.add('z-10');
      sidebar.classList.add('bottom-0');
      sidebar.classList.add('h-[92%]');
    }

    function backToIndex() {
      window.location.href = "/";
      localStorage.clear();
    }
  </script>
  <script src="js/panel_routing.js"></script>
</body>

</html>