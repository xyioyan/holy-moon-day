document.addEventListener('DOMContentLoaded', () => {
    loadRecentAlerts();
    loadStatistics();
});

// Function to load recent alerts
function loadRecentAlerts() {
    fetch('../php/fetch_alerts.php') // PHP endpoint to get recent alerts
        .then(response => {return response.json()})
        .then(alerts => {
            console.log(alerts);
              const alertList = document.getElementById('alert-list');
            alertList.innerHTML = ''; // Clear the existing list

            if (alerts.length === 0) {
                alertList.textContent = 'No recent alerts.';
                return;
            }

            alerts.forEach(alert => {
                const alertItem = document.createElement('div');
                alertItem.classList.add('alert-item');
                alertItem.innerHTML = `
                    <strong>Time:</strong> ${alert.time} <br>
                    <strong>Type:</strong> ${alert.type} <br>
                    <strong>Details:</strong> ${alert.details}
                `;
                alertList.appendChild(alertItem);
            });
        })
        .catch(error => console.error('Error fetching alerts:', error));
}

// Function to load system statistics
function loadStatistics() {
    fetch('../php/fetch_statistics.php') // PHP endpoint to get statistics
        .then(response => response.json())
        .then(stats => {
            const statsContainer = document.getElementById('stats-container');
            statsContainer.innerHTML = `
                <div class="stat-item">
                    <strong>Total Alerts:</strong> ${stats.totalAlerts}
                </div>
                <div class="stat-item">
                    <strong>Critical Alerts:</strong> ${stats.criticalAlerts}
                </div>
                <div class="stat-item">
                    <strong>Active Rules:</strong> ${stats.activeRules}
                </div>
            `;
        })
        .catch(error => console.error('Error fetching statistics:', error));
}