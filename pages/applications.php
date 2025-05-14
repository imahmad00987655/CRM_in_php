<?php
$user_role =  $_GET['user_role'];
?>

<div class="w-full flex flex-col h-full justify-between">
    <!-- top bar -->
    <div class="flex static items-center overflow-scroll scrollbar-none bg-white justify-between gap-5 px-10 w-full h-fit py-3">
        <div class="flex items-center gap-5 w-full">
            <input type="text" id="search" placeholder="Search here..." oninput="fetchData()" class="px-3 py-1 text-lg outline-none border-gray-300 rounded-lg border-2 border-red-300 w-full sm:w-auto" />
            <?php if ( !in_array($user_role , ['sales_agent'])) : ?>
            <div class="flex gap-2 items-center">
                <select id="statusFilter" class="px-3 py-2 outline-none border-gray-300 rounded-lg border-2 border-red-300 w-full sm:w-auto">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Process">In Process</option>
                    <option value="Applied">Applied</option>Applied
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="flex gap-2 items-center">
            <?php if ( !in_array($user_role , ['sales_agent'])) : ?>
            <input type="date" id="startDate" class="border rounded-lg border-red-300 p-2">
            <input type="date" id="endDate" class="border rounded-lg border-red-300 p-2">
            <?php endif; ?>
            <button onclick="fetchData()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Apply</button>
            <button onclick="resetFilter()" class="text-black w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5" viewBox="0 0 512 512">
                    <path d="M142.9 142.9c-17.5 17.5-30.1 38-37.8 59.8c-5.9 16.7-24.2 25.4-40.8 19.5s-25.4-24.2-19.5-40.8C55.6 150.7 73.2 122 97.6 97.6c87.2-87.2 228.3-87.5 315.8-1L455 55c6.9-6.9 17.2-8.9 26.2-5.2s14.8 12.5 14.8 22.2l0 128c0 13.3-10.7 24-24 24l-8.4 0c0 0 0 0 0 0L344 224c-9.7 0-18.5-5.8-22.2-14.8s-1.7-19.3 5.2-26.2l41.1-41.1c-62.6-61.5-163.1-61.2-225.3 1zM16 312c0-13.3 10.7-24 24-24l7.6 0 .7 0L168 288c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6 0-.7 0-7.6z" />
                </svg>
            </button>
        </div>
        <?php if (in_array($user_role , ['admin','data_entry_agent','manager'])): ?>
            <button onclick="loadComponent('newApplication.php', 'New Application')" class="bg-red-600 px-5 text-xl text-white hover:bg-black h-full flex text-nowrap items-center justify-center rounded-xl">
                + Create
            </button>
        <?php endif; ?>

    </div>
    <!-- Table -->
    <div class="flex w-full h-full overflow-scroll">
        <div class="w-full h-fit">
            <table class="w-full h-full bg-white border-collapse border border-gray-300 rounded-md shadow">
                <thead>
                    <tr class="bg-red-800 text-white  px-5 py-3">
                        <th class="p-3 w-fit text-left text-nowrap"></th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Name</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">ID Card #</th>
                        <th id="amountHeader" class="p-3 min-w-36 max-w-fit text-left text-nowrap">Total Amount</th>
                        <th id="cityHeader" class="p-3 min-w-36 max-w-fit text-left text-nowrap">City</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Country</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Visa Agent</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Data Entry By</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Application Date</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Deadline Date</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Status</th>
<!--                        <th class="p-3 min-w-fit max-w-fit items-center text-left text-nowrap">Limit</th>-->
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <div class="flex items-center justify-between py-3 px-5 bg-gray-100">
        <span>Total Applications: <strong id="totalApplications">0</strong></span>

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
    document.getElementById('statusFilter').value = '';

    document.getElementById('itemsPerPage').addEventListener('change', function() {
        fetchData();
    });

    // Update when filter changes
    document.getElementById('statusFilter').addEventListener('change', function() {
        fetchData();
    });

    // Handle search
    document.getElementById('search').addEventListener('input', function(e) {
        searchQuery = e.target.value;
        currentPage = 1; // Reset to first page
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
        let searchQuery = document.getElementById('search')?.value.trim() || '';
        let statusFilter = document.getElementById('statusFilter')?.value || '';
        let itemsPerPage = document.getElementById('itemsPerPage')?.value || 10;
        let startDate = document.getElementById('startDate')?.value || '';
        let endDate = document.getElementById('endDate')?.value || '';

        try {
            const url =
                `../backend/application_controller.php?page=${page}&limit=${itemsPerPage}` +
                (searchQuery ? `&search=${encodeURIComponent(searchQuery)}` : '') +
                (statusFilter ? `&status=${encodeURIComponent(statusFilter)}` : '') +
                (startDate ? `&start_date=${encodeURIComponent(startDate)}` : '') +
                (endDate ? `&end_date=${encodeURIComponent(endDate)}` : '');

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            const {
                data,
                total,
                role
            } = await response.json();

            totalPages = Math.ceil(total / itemsPerPage);
            currentPage = page;

            document.getElementById('currentPage').innerText = currentPage;
            document.getElementById('totalPages').innerText = totalPages;
            document.getElementById('totalApplications').innerText = total;

            if (role !== 'admin' ) {
                document.getElementById('cityHeader').style.display = 'none';
                document.getElementById('amountHeader').style.display = 'none';
                
            }
        

            populateTable(data);
            updatePaginationButtons();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function resetFilter() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        fetchData(); // Refresh data with default values
    }


    function populateTable(data) {
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';
        data.forEach((item, index) => {
            let statusColor =
                item.status === 'Completed' ?
                'bg-green-500' :
                item.status === 'In Process' ?
                'bg-blue-500' :
                item.status === 'Cancelled' ?
                'bg-red-500' :
                item.status === 'Applied' ?
                'bg-yellow-500' :
                'bg-gray-300';

          let remaining_balance = item.total_amount - item.advance_amount;
          let overdueAmountStyle = '';

          let currentDate = new Date().toISOString().split('T')[0];
          if (remaining_balance > 0 && item.deadline_date && item.deadline_date < currentDate) {
            overdueAmountStyle = 'background-color: #ffcccc;';
          }
            let rowHTML = `
        <tr class="border-b" style="${overdueAmountStyle}">
            <td class="px-5 text-gray-700 text-nowrap">
                <div class="w-5 h-5 rounded-full ${statusColor}"></div>
            </td>
            <td class="px-4 py-4 text-gray-900 font-medium">${item.applicant_name}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.applicant_cnic}</td>
            ${userRole === 'admin' ? `<td class="px-4 py-4 text-gray-700 text-nowrap">${'Rs ' +item.total_amount}</td>` : ''}
            ${userRole === 'admin' ? `<td class="px-4 py-4 text-gray-700 text-nowrap">${item.application_city_name}</td>` : ''}
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.application_country}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.visa_agent_name}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.data_entry_agent_name}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.created_at}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.deadline_date_parsed ?? 'N/A'}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.status}</td>
<!--            <td class="px-4 py-4 w-fit text-lg text-nowrap"> <div class="text-white w-fit px-2 py-1 rounded-lg  ${item.application_limit  && 'bg-black'} ${item.application_limit <=0 ? 'text-red-600' :'text-gray-700'}">${item.application_limit ?? ''}</div></td>-->
            <td class="px-4 py-4 relative flex items-center w-full gap-3">
                <button onclick='viewApplication(${JSON.stringify(item)})'  class="px-3 py-2 text-sm text-white bg-gray-800 rounded-md hover:bg-black">View</button>
                ${
                    (userRole === 'admin' || userRole === 'visa_agent'  || userRole === 'manager')
										? `<button onclick="toggleDropdown(${index})" class="w-9 h-9 text-2xl bg-gray-200 rounded-md hover:bg-gray-300">⋮</button>`
										: ''
								}
                <div  id="dropdown-${index}" class="hidden absolute top-12 right-0 mt-2 w-40 bg-white border rounded-md shadow-md z-10">

            ${userRole === 'visa_agent' ? (item.status != 'Applied' ?
                `
                <button class="block w-full px-4 py-2 text-blue-600 hover:bg-blue-100 border-b" onclick="updateStatus(${item.id}, 'Applied', ${index})">Applied</button>`: ``)
                :
                `<button class="block w-full px-4 py-2 text-blue-600 hover:bg-blue-100 border-b" onclick="updateStatus(${
											item.id
										}, 'In Process', ${index})">In Process</button>
                    <button class="block w-full px-4 py-2 text-gray-600 hover:bg-gray-100 border-b" onclick="updateStatus(${
											item.id
										}, 'Pending', ${index})">Pending</button>
                    <button class="block w-full px-4 py-2 text-red-600 hover:bg-red-100 border-b" onclick="updateStatus(${
											item.id
										}, 'Cancelled', ${index})">Cancelled</button>
                    <button class="block w-full px-4 py-2 text-green-600 hover:bg-green-100" onclick="updateStatus(${
											item.id
										}, 'Completed', ${index})">Completed</button>
                    <button class="block w-full px-4 py-2 bg-red-600 text-white hover:bg-red-700" onclick="deleteApp(${
											item.id
										}, ${index})">Delete</button>`
            }
                </div>
            </td>
        </tr>
        `;
            // Append the generated row HTML to the table body
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


    function toggleDropdown(index) {
        let dropdown = document.getElementById(`dropdown-${index}`);

        // Close all other dropdowns before opening the selected one
        document.querySelectorAll('.dropdown-menu').forEach((menu) => {
            if (menu !== dropdown) {
                menu.classList.add('hidden');
            }
        });

        // Toggle the selected dropdown
        dropdown.classList.toggle('hidden');
    }

    async function updateStatus(id, status, index) {
        try {
            let response = await fetch('../backend/update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id,
                    status,
                }),
            });

            let result = await response.json();
            toggleDropdown(index);
            alert(result.message);
            fetchData(); // Refresh table
        } catch (error) {
            console.error('Update error:', error);
        }
    }

    function viewApplication(data) {
        loadComponent('newApplication.php', data.applicant_name + ' Application', true, data)
    }

    function deleteApp(appid, index) {
        if (!confirm("Are you sure you want to delete this application?")) {
            return;
        }
        fetch("../backend/application_controller.php", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id: appid,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toggleDropdown(index);
                    alert(data.message);
                    fetchData(); // Refresh data after deletion
                } else {
                    toggleDropdown(index);
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error deleting application:", error);
                alert("Failed to delete application. Please try again.");
            });
    }
</script>