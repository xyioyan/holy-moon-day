function fetchLogs() {
    fetch('../php/fetch_logs.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            const logList = document.getElementById('log-list');
            logList.innerHTML = ''; // Clear existing logs

            if (data.success && data.data.length > 0) {
                data.data.forEach(log => {
                    const logItem = document.createElement('div');
                    logItem.classList.add('log-item');
                    logItem.textContent = `[${log.created_at}] ${log.log_message}`;
                    logList.appendChild(logItem);
                });
            } else {
                logList.textContent = data.error || 'No logs available.';
            }
        })
        .catch(error => {
            console.error('Error fetching logs:', error);
        });
}

// Call fetchLogs periodically for real-time updates
setInterval(fetchLogs, 5000); // Fetch logs every 5 seconds
