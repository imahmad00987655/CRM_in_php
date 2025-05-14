<?php
# Initialize the session
session_start();
$loggedUserRole = $_SESSION['user_role'] ?? 'guest'; // Prevent undefined index
$user_id = $_SESSION['id'] ?? 0;
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="./css/tailwind.css" rel="stylesheet" />
    <link rel="icon" href="src/logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="src/logo.png" type="image/x-icon">
</head>

<body class="flex flex-col items-center gap-10 w-screen h-screen bg-red-100 bg-opacity-50 p-5"
    style="background-image: url('src/bgpage.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="z-10 h-fit w-full justify-center flex pt-5 items-center gap-5 text-xl font-semibold">
        <a href='/' class="cursor-pointer hover:bg-red-600 hover:text-white px-5 py-2  bg-white text-red-600 hover:text-white hover:bg-red-600 rounded-xl text-xl leading-none w-fit text-center">Home</a>
        <?php if ($isLoggedIn): ?>
            <a href="panel.php" class="cursor-pointer hover:bg-red-600 hover:text-white px-5 py-2  bg-white text-red-600 hover:text-white hover:bg-red-600 rounded-xl text-xl leading-none w-fit text-center">Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="w-full sm:w-2/3 lg:w-1/2 h-fit flex flex-col gap-10 items-center justify-between bg-white rounded-3xl shadow-lg px-5 py-10">
        <h1 class="w-full text-3xl text-center font-medium">Notices</h1>
        <?php if ($loggedUserRole === 'admin' || $loggedUserRole == 'manager') : ?>
            <form id="noticeForm" class="w-full rounded-xl h-fit flex flex-col gap-2 justify-between bg-gray-100 p-2">
                <input type="hidden" id="noticeId" value="">
                <textarea id="noticeText" placeholder="Enter notice here..." required class="bg-gray-100 p-2 h-24 rounded-lg outline-none focus:bg-white"></textarea>
                <button type="submit" id="submitButton" class="bg-gray-200 rounded-lg w-fit px-3 py-2 font-medium hover:bg-black hover:text-white">Post Notice</button>
            </form>
        <?php endif; ?>

        <div id="noticesList" class="w-full flex flex-col gap-3"></div>
    </div>

    <script>
        // Set loggedUserRole before any JS functions
        var loggedUserRole = "<?php echo $loggedUserRole; ?>";
        var userId = "<?php echo $user_id; ?>";

        function fetchNotices() {
            fetch("backend/notice_controller.php", {
                    method: "GET"
                })
                .then(response => response.json())
                .then(data => {
                    const noticesList = document.getElementById("noticesList");
                    noticesList.innerHTML = "";

                    data.forEach(notice => {
                        const noticeDiv = document.createElement("div");
                        noticeDiv.className = "flex w-full bg-gray-100 rounded-xl p-3 items-end justify-between";

                        let adminActions = "";

                        if ((loggedUserRole === "admin" || loggedUserRole === "manager") && notice.user_id && notice.user_id == userId ) {
                            adminActions = `
                        <div class="w-fit items-end gap-2 flex h-full">
                            <button class="rounded-lg bg-green-500 py-1.5 px-2.5 text-white hover:bg-green-600 cursor-pointer"
                                onclick="editNotice(${notice.id}, '${notice.notice_text.replace(/'/g, "\\'")}')">Edit</button>
                            <button class="rounded-lg bg-red-500 py-1.5 px-2.5 text-white hover:bg-red-600 cursor-pointer"
                                onclick="deleteNotice(${notice.id})">Delete</button>
                        </div>`;
                        }

                        console.log(notice);
                        if(notice.user_role && notice.user_role == 5){
                          noticeDiv.innerHTML = `
                    <div class="w-full h-full flex flex-col gap-2">
                        <p class="text-lg font-medium">${notice.notice_text}  <small class="text-gray-600"> (Posted by Manager: ${notice.name ?? ''}) </small></p>
                        <small class="text-gray-600">${notice.created_at}</small>
                    </div>
                    ${adminActions}
                `;
                        }else{
                          noticeDiv.innerHTML = `
                    <div class="w-full h-full flex flex-col gap-2">
                        <p class="text-lg font-medium">${notice.notice_text}</p>
                        <small class="text-gray-600">${notice.created_at}</small>
                    </div>
                    ${adminActions}
                `;
                        }



                        noticesList.appendChild(noticeDiv);
                    });
                })
                .catch(error => console.error("Error fetching notices:", error));
        }


        document.addEventListener("DOMContentLoaded", () => {
            fetchNotices();

            const form = document.getElementById("noticeForm");
            if (form) {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();
                    const noticeId = document.getElementById("noticeId").value;
                    const noticeText = document.getElementById("noticeText").value;

                    const options = {
                        method: noticeId ? "PUT" : "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: noticeId ? `id=${noticeId}&noticeText=${encodeURIComponent(noticeText)}` : `noticeText=${encodeURIComponent(noticeText)}`,
                    };

                    fetch("backend/notice_controller.php", options)
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message || data.error);
                            fetchNotices();
                            document.getElementById("noticeId").value = "";
                            document.getElementById("noticeText").value = "";
                            document.getElementById("submitButton").innerText = "Post Notice";
                        })
                        .catch(error => console.error("Error:", error));
                });
            }
        });

        function editNotice(id, text) {
            document.getElementById("noticeId").value = id;
            document.getElementById("noticeText").value = text;
            document.getElementById("submitButton").innerText = "Update Notice";
        }

        function deleteNotice(id) {
            if (confirm("Are you sure you want to delete this notice?")) {
                fetch("backend/notice_controller.php", {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message || data.error);
                        fetchNotices();
                    })
                    .catch(error => console.error("Error:", error));
            }
        }

        // fetchNotices();
    </script>


</body>

</html>