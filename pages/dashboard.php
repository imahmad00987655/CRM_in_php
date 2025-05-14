<!-- Bar Graphs Grid -->
<div class="flex flex-col gap-5 w-full h-full overflow-scroll p-5">
    <!-- Date Range Filter -->
    <div class="bg-white p-3 rounded-lg shadow-lg w-full h-fit">
        <h2 class="text-xl font-semibold mb-4">Date Range Filter</h2>
        <div class="flex flex-col sm:flex-row gap-5">
            <div class="flex flex-col gap-1">
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" class="border rounded-lg p-2">
            </div>
            <div class="flex flex-col gap-1">
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" class="border rounded-lg  p-2">
            </div>
            <div class="flex gap-3 w-full h-fit place-self-end">

                <button onclick="fetchApplications()" class="h-fit place-self-end bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Apply Filter
                </button>
                <button onclick="resetFilter()" class="h-fit place-self-end bg-black text-white px-4 py-2 rounded-lg">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-lg shadow-lg items-center flex flex-col gap-5 w-full h-fit">
        <h2 class="text-xl font-semibold mb-4">Total Applications & Trend</h2>
        <div class="items-center flex flex-col xl:flex-row gap-10 w-full h-fit">
            <div class="w-full h-full flex justify-center items-center">
                <canvas id="totalApplicationsChart"></canvas>
            </div>
            <div class="w-full h-full flex justify-center items-center">
                <canvas id="applicationsTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="bg-white p-3 rounded-lg shadow-lg items-center flex flex-col gap-5 w-full h-fit">
        <h2 class="text-xl font-semibold mb-4">Applications by City (Bar & Pie)</h2>
        <div class="items-center flex flex-col xl:flex-row gap-5 w-full h-fit">
            <div class="w-full h-full flex justify-center items-center">
                <canvas id="cityApplicationsChart" class="max-w-full h-full"></canvas>
            </div>
            <div class="w-fit h-fit flex justify-center items-center">
                <canvas id="cityApplicationsPieChart" class="min-w-fit max-w-fit max-h-[280px]"></canvas>
            </div>
        </div>
    </div>
</div>


<script>
    // Global chart registry
    var chartRegistry = {};

    function fetchApplications() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Destroy all existing charts before fetching new data
        destroyAllCharts();

        Promise.all([
                fetch('backend/application_controller.php').then(response => response.json()),
                fetch('backend/officers_controller.php').then(response => response.json())
            ])
            .then(([applicationsData, agentsData]) => {
                let statusCounts = {
                    Pending: 0,
                    'In Process': 0,
                    'Applied': 0,
                    Completed: 0,
                    Cancelled: 0
                };
                let dateCounts = {
                    Pending: {},
                    'In Process': {},
                    'Applied': {},
                    Completed: {},
                    Cancelled: {}
                };
                let cityCounts = {};
                let cityStatusCounts = {};
                let agentCityMap = {};

                agentsData.data.forEach(agent => {
                    agentCityMap[agent.name] = agent.city || 'Unknown';
                });

                const filteredApplications = applicationsData.data.filter(a => {
                    const appDate = new Date(formatDate(a.created_at));
                    const start = startDate ? new Date(startDate) : null;
                    const end = endDate ? new Date(endDate) : null;

                    if (start && end) return appDate >= start && appDate <= end;
                    else if (start) return appDate >= start;
                    else if (end) return appDate <= end;
                    return true;
                });

                filteredApplications.forEach(a => {
                    let status = a.status || 'Pending';
                    let formattedDate = formatDate(a.created_at);
                    let date = formattedDate;
                    let agentName = a.agent;
                    let city = a.application_city_name || 'Unknown';

                    if (!cityCounts[city]) cityCounts[city] = 0;
                    cityCounts[city]++;

                    if (!cityStatusCounts[city]) {
                        cityStatusCounts[city] = {
                            Pending: 0,
                            'In Process': 0,
                            'Applied': 0,
                            Completed: 0,
                            Cancelled: 0
                        };
                    }
                    cityStatusCounts[city][status]++;
                    statusCounts[status]++;

                    if (!dateCounts[status][date]) dateCounts[status][date] = 0;
                    dateCounts[status][date]++;
                });

                let cities = Object.keys(cityCounts);
                let totalApplications = Object.values(cityCounts);
                let pendingCounts = cities.map(city => cityStatusCounts[city].Pending);
                let inProcessCounts = cities.map(city => cityStatusCounts[city]['In Process']);
                let AppliedCounts = cities.map(city => cityStatusCounts[city]['Applied']);
                let completedCounts = cities.map(city => cityStatusCounts[city].Completed);
                let cancelledCounts = cities.map(city => cityStatusCounts[city].Cancelled);

                // Render charts with filtered data
                chartRegistry['totalApplicationsChart'] = renderBarChart('totalApplicationsChart', 'Total Applications by Status', Object.keys(statusCounts), Object.values(statusCounts));
                chartRegistry['applicationsTrendChart'] = renderStatusTrendsChart('applicationsTrendChart', dateCounts);
                chartRegistry['cityApplicationsPieChart'] = renderPieChart('cityApplicationsPieChart', 'Applications by City', cities, totalApplications);
                chartRegistry['cityApplicationsChart'] = renderStackedBarChart('cityApplicationsChart', 'Applications by City & Status', cities, {
                    Pending: pendingCounts,
                    'In Process': inProcessCounts,
                    'Applied' : AppliedCounts,
                    Completed: completedCounts,
                    Cancelled: cancelledCounts
                });
            })
            .catch(error => console.error('Error fetching applications:', error));
    }

    function resetFilter() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        fetchApplications();
    }

    function destroyAllCharts() {
        Object.keys(chartRegistry).forEach(chartId => {
            if (chartRegistry[chartId] instanceof Chart) {
                chartRegistry[chartId].destroy();
                delete chartRegistry[chartId];
            }
        });
    }

    function renderBarChart(canvasId, label, labels = [], data = []) {
        let canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error(`Canvas '${canvasId}' not found.`);
            return null;
        }

        let ctx = canvas.getContext('2d');
        canvas.style.width = '100%';
        canvas.style.maxHeight = '400px';

        const backgroundColors = data.map((_, i) => ['#FF6384', '#36A2EB', '#4BC0C0', '#FF9F40'][i % 4]);

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function renderPieChart(canvasId, label, labels = [], data = []) {
        let ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: ['#FF6384', '#36A2EB', '#4BC0C0', '#FF9F40']
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function renderStackedBarChart(canvasId, label, labels, data) {
        let ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Pending',
                        data: data.Pending,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                    },
                    {
                        label: 'In Process',
                        data: data['In Process'],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    },
                    {
                        label: 'Applied',
                        data: data.Completed,
                        backgroundColor: 'rgba(192, 176, 75, 0.7)'
                    },
                    {
                        label: 'Completed',
                        data: data.Completed,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)'
                    },
                    {
                        label: 'Cancelled',
                        data: data.Cancelled,
                        backgroundColor: 'rgba(254, 65, 55, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    }

    function renderStatusTrendsChart(canvasId, dateCounts) {
        let ctx = document.getElementById(canvasId).getContext('2d');
        let allDates = Array.from(new Set(Object.values(dateCounts).flatMap(dates => Object.keys(dates)))).sort();
        Object.keys(dateCounts).forEach(status => {
            allDates.forEach(date => {
                if (!dateCounts[status][date]) dateCounts[status][date] = 0;
            });
        });

        let datasets = Object.keys(dateCounts).map(status => ({
            label: status,
            data: allDates.map(date => dateCounts[status][date]),
            borderColor: status === 'Pending' ? '#FF6384' : status === 'In Process' ? '#36A2EB' : status === 'Completed' ? '#4BC0C0' : '#FF9F40',
            backgroundColor: status === 'Pending' ? 'rgba(255, 99, 132, 0.2)' : status === 'In Process' ? 'rgba(54, 162, 235, 0.2)' : status === 'Completed' ? 'rgba(75, 192, 192, 0.2)' : 'rgba(255, 159, 64, 0.2)',
            borderWidth: 2,
            fill: true
        }));

        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: allDates,
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function formatDate(dateString) {
        let cleanedDate = dateString.replace(/(\d+)(st|nd|rd|th)/, '$1');
        let date = new Date(cleanedDate);
        return date.toISOString().split('T')[0];
    }

    document.addEventListener('DOMContentLoaded', fetchApplications);
</script>