<?php
# Initialize the session
session_start();
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$loggedUserRole = $_SESSION['user_role'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Document Viewer</title>
    <link href="./css/tailwind.css" rel="stylesheet" />
    <link rel="icon" href="src/logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="src/logo.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body class="w-full bg-gray-100 p-5 flex flex-col items-center gap-10 h-screen overflow-clip bg-red-100 bg-opacity-50" style="background-image: url('src/bgpage.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="h-fit w-full justify-center flex pt-5 items-center gap-5 text-xl font-semibold">
        <a href='/' class="cursor-pointer hover:bg-red-600 hover:text-white px-5 py-2  bg-white text-red-600 hover:text-white hover:bg-red-600 rounded-xl text-xl leading-none w-fit text-center">Home</a>
        <?php if ($isLoggedIn): ?>
            <a href="panel.php" class="cursor-pointer hover:bg-red-600 hover:text-white px-5 py-2  bg-white text-red-600 hover:text-white hover:bg-red-600 rounded-xl text-xl leading-none w-fit text-center">Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="flex flex-col gap-5 min-h-fit max-h-[90%] bg-white p-5 rounded-3xl shadow-lg w-full max-w-xl">
        <h1 class="text-2xl font-bold text-center">Select Country</h1>
        <div class="w-full h-fit flex flex-col gap-2">
            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
            <select id="country" class="mt-1 block w-full p-2 border border-gray-300 outline-none rounded-lg">
                <option value="">-- Select a Country --</option>
            </select>
        </div>
        <div id="documents-container" class="hidden w-full gap-5 h-full items-centerr justify-center overflow-hidden">
            <div class="flex justify-between w-full">
                <h2 class="text-xl font-semibold mb-5">Required Documents:</h2>
                <?php if ($loggedUserRole == 'admin'): ?>
                    <button id="create-document" class="px-2.5 h-fit w-fit py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Add</button>
                <?php endif; ?>
            </div>
            <ul id="document-list" class="flex flex-col list-disc w-full pl-5 gap-3 text-gray-700 h-96 overflow-y-scroll scrollbar-none"></ul>
            <div class="flex gap-3 mt-4">
                <button id="export-pdf" class="px-2.5 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">Export PDF</button>
                <button id="print-documents" class="px-2.5 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Print</button>
            </div>
        </div>
    </div>
    <script src="/js/visa_information.js"></script>
    <script>
        window.currentUser = {
            id: "<?php echo $_SESSION['id'] ?? ''; ?>",
            username: "<?php echo $_SESSION['username'] ?? ''; ?>",
            user_role: "<?php echo $_SESSION['user_role'] ?? 'guest'; ?>"
        };
    </script>
</body>

</html>