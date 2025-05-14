<?php
$user_role =  $_GET['user_role'];
?>

<div class="w-full flex flex-col h-full justify-between">
    <!-- top bar -->
    <div class="flex static items-center bg-white justify-between gap-5 px-10 w-full h-fit py-3">
        <div class="flex items-center gap-5 w-full">
            <input type="text" id="search" placeholder="Search here..." oninput="fetchData()" class="px-3 py-1 text-lg outline-none border-gray-300 rounded-lg border-2 border-red-300 w-full sm:w-auto" />
        </div>
        <div class="flex gap-2 items-center">
            <input type="date" id="startDate" class="border rounded-lg border-red-300 p-2">
            <input type="date" id="endDate" class="border rounded-lg border-red-300 p-2">
            <button onclick="fetchData()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Apply</button>
            <button onclick="resetFilter()" class="text-black w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5" viewBox="0 0 512 512">
                    <path d="M142.9 142.9c-17.5 17.5-30.1 38-37.8 59.8c-5.9 16.7-24.2 25.4-40.8 19.5s-25.4-24.2-19.5-40.8C55.6 150.7 73.2 122 97.6 97.6c87.2-87.2 228.3-87.5 315.8-1L455 55c6.9-6.9 17.2-8.9 26.2-5.2s14.8 12.5 14.8 22.2l0 128c0 13.3-10.7 24-24 24l-8.4 0c0 0 0 0 0 0L344 224c-9.7 0-18.5-5.8-22.2-14.8s-1.7-19.3 5.2-26.2l41.1-41.1c-62.6-61.5-163.1-61.2-225.3 1zM16 312c0-13.3 10.7-24 24-24l7.6 0 .7 0L168 288c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6 0-.7 0-7.6z" />
                </svg>
            </button>
        </div>
        <button onclick="exportToCSV(billingData)" class="bg-green-500 text-white text-nowrap px-4 py-2 rounded-lg hover:bg-green-600">Export to CSV</button>
        <button onclick="printBillingTable(billingData)" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-blue-800">Print</button>
    </div>
    <!-- Table -->
    <div class="flex w-full h-full overflow-scroll">
        <div class="w-full h-fit">
            <table id="billingTable" class="w-full h-full bg-white border-collapse border border-gray-300 rounded-md shadow">
                <thead>
                    <tr class="bg-red-800 text-white  px-5 py-3">
                        <th class="p-3 w-fit text-left text-nowrap"></th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Name</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">ID Card #</th>
                        <th id="amountHeader" class="p-3 min-w-36 max-w-fit text-left text-nowrap">Total Amount</th>
                        <th id="amountHeader" class="p-3 min-w-36 max-w-fit text-left text-nowrap">Paid</th>
                        <th id="amountHeader" class="p-3 min-w-36 max-w-fit text-left text-nowrap">Balance</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Country</th>
                        <th id="agent" class="p-3 min-w-36 max-w-fit text-left text-nowrap">Agent</th>
                        <th class="p-3 min-w-36 max-w-fit text-left text-nowrap">Application Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>


    <!-- Pagination -->
    <div class="w-full bg-gray-100 py-2 gap-10 px-5 flex items-center">
        <span>Total Advance/Paid: <strong id="totalAdvance">Rs 0</strong></span> |
        <span>Total Balance: <strong id="totalBalance">Rs 0</strong></span>
    </div>
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
    var billingData = ''

    document.getElementById('search').value = '';

    document.getElementById('itemsPerPage').addEventListener('change', function() {
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
        let searchQuery = document.getElementById('search').value.trim();
        let itemsPerPage = document.getElementById('itemsPerPage').value;

        // Get the start and end dates from input fields
        let startDate = document.getElementById('startDate').value;
        let endDate = document.getElementById('endDate').value;

        try {
            let url = `../backend/application_controller.php?page=${page}&limit=${itemsPerPage}` +
                (searchQuery ? `&search=${encodeURIComponent(searchQuery)}` : '') +
                (startDate ? `&start_date=${encodeURIComponent(startDate)}` : '') +
                (endDate ? `&end_date=${encodeURIComponent(endDate)}` : '');

            const response = await fetch(url);
            const {
                data,
                total,
                total_advance,
                total_balance
            } = await response.json();

            // Store fetched data
            billingData = {
                data,
                total,
                total_advance,
                total_balance
            };
            totalPages = Math.ceil(total / itemsPerPage);
            currentPage = page;

            // Update UI
            document.getElementById('currentPage').innerText = currentPage;
            document.getElementById('totalPages').innerText = totalPages;
            document.getElementById('totalApplications').innerText = total;
            document.getElementById('totalAdvance').innerText = 'Rs ' + total_advance;
            document.getElementById('totalBalance').innerText = 'Rs ' + total_balance;

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
            let statusColor =
                item.status === 'Completed' ?
                'bg-green-500' :
                item.status === 'In Process' ?
                'bg-blue-500' :
                item.status === 'Applied' ?
                'bg-yellow-500' :
                item.status === 'Cancelled' ?
                'bg-red-500' :
                'bg-gray-300';
            let advancePayment = item.advance_amount;
            const balancePayment = item.total_amount - item.advance_amount;
            if (advancePayment == item.total_amount) {
                advancePayment = 'ALL PAID'
            }
            // }
            let rowHTML = `
            <tr class="border-b">
            <td class="px-5 text-gray-700 text-nowrap">
                <div class="w-5 h-5 rounded-full ${statusColor}"></div>
            </td>
            <td class="px-4 py-4 text-gray-900 font-medium">${item.applicant_name}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.applicant_cnic}</td>
           <td class="px-4 py-4 text-gray-700 text-nowrap font-medium">${'Rs ' + item.total_amount}</td>
           <td class="px-4 py-4 text-gray-700 text-nowrap ${advancePayment == 'ALL PAID' && 'text-green-500 font-semibold'}">${advancePayment !== 'ALL PAID' ? 'Rs ' + advancePayment :advancePayment}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap font-medium ${balancePayment !== 0 && 'text-red-500'}">${advancePayment !== 'ALL PAID' ? 'Rs ' +balancePayment:''}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.application_country}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.visa_agent_name}</td>
            <td class="px-4 py-4 text-gray-700 text-nowrap">${item.created_at}</td>
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

    function resetFilter() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        fetchData(); // Reload data without filter
    }

    function exportToCSV(data, filename = 'billing_data.csv') {
        let table = document.getElementById("billingTable");
        if (!table) {
            alert("Billing table not found!");
            return;
        }

        let rows = table.querySelectorAll("tr");
        let csvContent = [];

        // Extract table data
        rows.forEach(row => {
            let cols = row.querySelectorAll("th, td");
            let rowData = [];
            cols.forEach(col => rowData.push('"' + col.innerText.replace(/"/g, '""') + '"'));
            csvContent.push(rowData.join(","));
        });

        // Use the fetched data totals
        let totalAdvance = parseFloat(data.total_advance) || 0;
        let totalBalance = parseFloat(data.total_balance) || 0;
        let totalAmount = totalAdvance + totalBalance;

        // Format currency as PKR
        const formatPKR = (amount) => new Intl.NumberFormat('en-PK', {
            style: 'currency',
            currency: 'PKR'
        }).format(amount);

        // Append totals to CSV
        csvContent.push(""); // Empty row for spacing
        csvContent.push(`"Total Amount","${formatPKR(totalAmount)}"`);
        csvContent.push(`"Total Advance","${formatPKR(totalAdvance)}"`);
        csvContent.push(`"Total Balance","${formatPKR(totalBalance)}"`);

        // Create and trigger download
        let csvFile = new Blob([csvContent.join("\n")], {
            type: "text/csv"
        });
        let downloadLink = document.createElement("a");
        downloadLink.href = URL.createObjectURL(csvFile);
        downloadLink.download = filename;
        downloadLink.click();
    }

    function printBillingTable(data) {
        let table = document.getElementById("billingTable");
        if (!table) {
            alert("Billing table not found!");
            return;
        }

        let rows = table.querySelectorAll("tbody tr");
        let totalAmount = 0;

        // Loop through each row to calculate total amount (if needed)
        rows.forEach(row => {
            let amountCell = row.querySelector("td:nth-child(4)"); // 3rd column (Amount)
            if (amountCell) {
                let amount = parseFloat(amountCell.innerText.replace(/[^0-9.]/g, '')) || 0;
                totalAmount += amount;
            }
        });

        // Use the fetched data totals (total_advance, total_balance)
        let totalAdvance = parseFloat(data.total_advance) || 0;
        let totalBalance = parseFloat(data.total_balance) || 0;

        let tableContent = table.outerHTML;
        let printWindow = window.open("", "_blank");

        printWindow.document.write(`
        <html>
        <img src="src/logo.png" class="w-32" alt="Ali Baba Travel Advisor"/>
        <head>
            <title>Print Billing Data</title>
            <style>
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .total-row { font-weight: bold; background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h2>Billing Data</h2>
            ${tableContent}
            <table>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Amount</strong></td>
                    <td><strong>$${totalAmount.toFixed(2)}</strong></td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Advance</strong></td>
                    <td><strong>$${totalAdvance.toFixed(2)}</strong></td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Balance</strong></td>
                    <td><strong>$${totalBalance.toFixed(2)}</strong></td>
                    <td></td>
                </tr>
            </table>
        </body>
        </html>
        `);

        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
        }, 100);
    }
</script>