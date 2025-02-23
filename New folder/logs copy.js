// Initialize the severity distribution pie chart
const ctx = document.getElementById('logSeverityChart').getContext('2d');
const logSeverityChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['High', 'Medium', 'Low'],
        datasets: [{
            data: [0, 0, 0], // Initial empty data
            backgroundColor: ['#ff4d4d', '#ffc107', '#28a745']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            }
        }
    }
});

// Fetch logs and render table
function fetchLogs(page = 1, filters = {}) {
    const url = new URL('../php/fetch_logs.php', window.location.href);
    url.searchParams.append('action', 'get_logs');
    url.searchParams.append('page', page);

    // Add filters to URL
    for (const key in filters) {
        if (filters[key]) {
            url.searchParams.append(key, filters[key]);
        }
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                renderLogs(data.logs);
                updatePagination(data.page, data.totalPages);
            } else {
                console.error('Failed to fetch logs:', data.error);
            }
        })
        .catch(error => console.error('Error fetching logs:', error));
}

// Render logs in the table
function renderLogs(logs) {
    const tbody = document.querySelector('#system-logs tbody');
    tbody.innerHTML = ''; // Clear existing rows
    logs.forEach(log => {
        const row = `<tr>
            <td>${log.created_at}</td>
            <td>${log.event_type}</td>
            <td>${log.source_ip}</td>
            <td>${log.severity}</td>
            <td>${log.description}</td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

// Update pagination controls
function updatePagination(currentPage, totalPages) {
    const pageInfo = document.getElementById('page-info');
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages;

    document.getElementById('prev-page').onclick = () => fetchLogs(currentPage - 1);
    document.getElementById('next-page').onclick = () => fetchLogs(currentPage + 1);
}

// Fetch severity distribution and update chart
function fetchSeverityDistribution() {
    fetch('../php/fetch_logs.php?action=get_severity_distribution')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                logSeverityChart.data.datasets[0].data = [
                    data.severityCounts.high || 0,
                    data.severityCounts.medium || 0,
                    data.severityCounts.low || 0,
                ];
                logSeverityChart.update();
            } else {
                console.error('Failed to fetch severity distribution:', data.error);
            }
        })
        .catch(error => console.error('Error fetching severity distribution:', error));
}

// Apply filters on form submission
document.getElementById('filter-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const filters = {
        start_date: document.getElementById('start-date').value,
        end_date: document.getElementById('end-date').value,
        severity: document.getElementById('severity').value,
        event_type: document.getElementById('event-type').value,
    };
    fetchLogs(1, filters); // Fetch logs with filters
});

// Function to export logs as CSV
function exportLogs() {
    
// Ask for confirmation before proceeding with export
const confirmExport = window.confirm("Are you sure you want to export the logs?");

// If the user clicks "Cancel", do not proceed with the export
if (!confirmExport) {
    console.log("Export canceled by the user.");
    return; // Exit the function
}

    const filters = {
        start_date: document.getElementById('start-date').value,
        end_date: document.getElementById('end-date').value,
        severity: document.getElementById('severity').value,
        event_type: document.getElementById('event-type').value,
    };

    // Build URL for exporting with filters
    let url = new URL('../php/export_logs.php', window.location.href);
    url.searchParams.append('action', 'export_logs');

    // Append filters to URL
    for (const key in filters) {
        if (filters[key]) {
            url.searchParams.append(key, filters[key]);
        }
    }

    // Open the URL for export (this will trigger the PHP script to download the CSV)
    
    window.location.href = url;
}


// Initial fetch of logs and severity distribution
fetchLogs();
fetchSeverityDistribution();
