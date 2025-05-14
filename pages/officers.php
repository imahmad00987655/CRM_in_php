<?php
$user_role =  $_GET['user_role'];
?>

<div class="w-full flex flex-col h-full justify-between">
    <!-- top bar -->
    <div class="flex static items-center bg-white justify-between gap-5 px-10 w-full h-fit py-3">
        <div class="flex items-center gap-5 w-full">
            <input type="text" id="search" placeholder="Search here..." oninput="fetchData()" class="px-3 py-1 text-lg outline-none border-gray-300 rounded-lg border-2 border-red-300 w-full sm:w-auto" />
            <div class="flex gap-2 items-center">
                Filter by:
                <select id="userFilter" class="px-3 py-2 outline-none border-gray-300 rounded-lg border-2 border-red-300 w-full sm:w-auto">
                </select>
            </div>
        </div>
        <button onclick="loadComponent('newOfficer.php', 'New Officer')" class="bg-red-600 px-5 text-xl text-white hover:bg-black h-full flex text-nowrap items-center justify-center rounded-xl">
            + Add
        </button>
    </div>
    <!-- Table -->
    <div class="flex w-full h-full overflow-scroll">
        <div class="w-full h-fit">
            <table class="w-full h-full bg-white border-collapse border border-gray-300 rounded-md shadow">
                <thead>
                    <tr class="bg-red-800 text-white  px-5 py-3">
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">User</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Contact</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">City</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Total</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Pending</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">In Process</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Completed</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Cancelled</th>
                        <th class="p-3 max-w-fit text-left text-nowrap">Edit</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <div class="flex items-center justify-between py-3 px-5 bg-gray-100">
        <span>Total Officers: <strong id="totalApplications">0</strong></span>
        <div>
            <label for="itemsPerPage">Items per page:</label>
            <select id="itemsPerPage" class="p-1 border rounded">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="flex items-center gap-5">
            <button id="prevPage" onclick="changePage(-1)" class="py-2 px-5 bg-gray-200 rounded-md hover:bg-gray-300">Previous</button>
            <span>Page <strong id="currentPage">1</strong> of <strong id="totalPages">1</strong></span>
            <button id="nextPage" onclick="changePage(1)" class="py-2 px-5 bg-gray-200 rounded-md hover:bg-gray-300">Next</button>
        </div>
    </div>
</div>

<script>
    var currentPage = 1;
    var itemsPerPage = 10;
    var searchQuery = '';


    document.getElementById('search').value = '';
    document.getElementById('userFilter').value = '';

    document.getElementById('itemsPerPage').addEventListener('change', function() {
        fetchData();
    });
    // Handle search
    document.getElementById('search').addEventListener('input', function(e) {
        searchQuery = e.target.value;
        currentPage = 1; // Reset to first page
        fetchData();
    });
    document.getElementById('userFilter').addEventListener('change', function() {
        fetchData();
    });
    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            changePage(-1);
        }
    });
    document.getElementById('nextPage').addEventListener('click', function() {
        if (currentPage < totalPages) {
            changePage(1);
        }
    })

    // fetch visa applications
    async function fetchData(page = 1) {
        let searchQuery = document.getElementById('search').value.trim();
        let userFilter = document.getElementById('userFilter').value;
        itemsPerPage = document.getElementById('itemsPerPage').value;

        try {
            const url =
                `../backend/officers_controller.php?page=${page}&limit=${itemsPerPage}` +
                (searchQuery ? `&search=${encodeURIComponent(searchQuery)}` : '') +
                (userFilter ? `&user_role=${encodeURIComponent(userFilter)}` : '');


            const response = await fetch(url);
            const {
                data,
                total,
                role
            } = await response.json();
            totalPages = Math.ceil(total / itemsPerPage); // Update totalPages value based on the response
            currentPage = page; // Ensure currentPage is updated

            document.getElementById('currentPage').innerText = currentPage;
            document.getElementById('totalPages').innerText = totalPages;
            document.getElementById('totalApplications').innerText = total;
            populateTable(data);
            updatePaginationButtons();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function populateTable(data) {
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';
        data.forEach((item, index) => {
            let rowHTML = `
        <tr class="border-b">
            <td class="px-4 py-4 text-gray-900 capitalize font-medium"><div class="flex flex-col gap-1">${item.name}<span class="text-sm text-nowrap text-gray-600">${'CNIC: '+ item.cnic}</span><span class="text-xs text-nowrap font-medium text-gray-600 ${item.role_name == 'visa_agent' ?'text-red-500': item.role_name == 'admin' ?'text-blue-500':'text-orange-500'}">${item.role_name}</span></div></td>
            <td class="px-4 py-4 text-gray-700 text-nowrap"><div class="flex flex-col text-base">${item.phone_number}<span class="text-sm text-nowrap text-gray-600">${item.email}</span><span class="text-sm text-nowrap text-gray-600">${"DOB: "+item.date_of_birth}</span></div></td>
            <td class="px-4 py-4 capitalize text-gray-900">${item.city}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap text-2xl"><span class='w-fit py-1 px-2 rounded-lg bg-gray-100'>${item.total}</span></td>
            <td class="px-4 py-4 text-gray-700 text-nowrap text-2xl"><span class='w-fit py-1 px-2 rounded-lg bg-gray-400 text-white'>${item.pending}</span></td>
            <td class="px-4 py-4 text-gray-700 text-nowrap text-2xl"><span class='w-fit py-1 px-2 rounded-lg bg-blue-500 text-white'>${item.in_process}</span></td>
            <td class="px-4 py-4 text-gray-700 text-nowrap text-2xl"><span class='w-fit py-1 px-2 rounded-lg bg-green-500 text-white'>${item.completed}</span></td>
            <td class="px-4 py-4 text-gray-700 text-nowrap text-2xl"><span class='w-fit py-1 px-2 rounded-lg bg-red-500 text-white'>${item.cancelled}</span></td>
            <td class="px-4 py-4">
            <button onclick='editUser(${JSON.stringify(item)})' class="text-blue-500">Edit</button>
                <button onclick="deleteUser(${item.id})" class="text-red-500">Delete</button>
            </td>
        </tr>
        `;
            tbody.innerHTML += rowHTML;
        });
    }

    function updatePaginationButtons() {
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage >= totalPages;
    }

    function changePage(direction) {
        if ((direction === -1 && currentPage > 1) || (direction === 1 && currentPage < totalPages)) {
            currentPage += direction;
            fetchData(currentPage);
        }
        if (direction == 0) {
            currentPage = 1;
            fetchData(currentPage);
        }
    }

    function editUser(user) {
        loadComponent('newOfficer.php', 'Edit Officer', true, user);
    };
    fetchOptions('roles', 'userFilter');

    function fetchOptions(type, elementId, selectedValue, listing = false) {
        fetch(`../backend/officers_controller.php?fetch=${type}`)
            .then((response) => response.json())
            .then((data) => {
                const select = document.getElementById(elementId);
                select.innerHTML = `<option value="">ALL</option>`;
                data.forEach((item) => {
                    const itemName = type == 'roles' ? item.role_name : item.city_name;
                    let formattedName = itemName
                        .replace(/_/g, ' ') // Replace underscores with spaces
                        .replace(/\b\w/g, (char) => char.toUpperCase()); // Capitalize each word
                    let isSelected = itemName === selectedValue ? 'selected' : '';
                    select.innerHTML += `<option value="${item.id}" ${isSelected}>${formattedName}</option>`;
                });
            })
            .catch((error) => console.error(`Error fetching ${type}:`, error));
    }
    // Delete user
    async function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        try {
            const response = await fetch(`../backend/officers_controller.php?id=${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            if (result.success) {
                fetchData();
                alert('User deleted successfully');
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error deleting user:', error);
        }
    }
</script>