document.addEventListener("DOMContentLoaded", function () {
  // Fetch data from PHP JSON file
  fetch("../php/dashboard.php")
      .then(response => response.json())
      .then(data => {
          if (data.error) {
              console.error("Error:", data.error);
              return;
          }

          // Update statistics section
          document.getElementById("total-alerts").innerText = data.totalAlerts || "0";
          document.getElementById("blocked-ips").innerText = data.blockedIps || "0";
          document.getElementById("active-rules").innerText = data.activeRules || "0";

          // Update recent alerts
          const alertsTable = document.getElementById("alerts-table");
          alertsTable.innerHTML = "<tr><th>Timestamp</th><th>Type</th><th>Severity</th><th>Action Taken</th></tr>"; // Clear previous data
          data.recentAlerts.forEach(alert => {
              let row = `<tr>
                  <td>${alert.created_at}</td>
                  <td>${alert.type || "N/A"}</td>
                  <td>${alert.severity}</td>
                  <td>${alert.action || "N/A"}</td>
              </tr>`;
              alertsTable.innerHTML += row;
          });

          // Update detection rules
          const rulesTable = document.getElementById("rules-table");
          rulesTable.innerHTML = "<tr><th>Rule ID</th><th>Description</th><th>Status</th></tr>";
          data.rules.forEach(rule => {
              let row = `<tr>
                  <td>${rule.id}</td>
                  <td>${rule.rule_name}</td>
                  <td>${rule.is_active === "1" ? "Active" : "Inactive"}</td>
              </tr>`;
              rulesTable.innerHTML += row;
          });

          // Update notifications
          const notificationsList = document.getElementById("notifications-list");
          notificationsList.innerHTML = "";
          data.notifications.forEach(notification => {
              notificationsList.innerHTML += `<li>${notification}</li>`;
          });

          // Update logs summary
          const logsList = document.getElementById("logs-list");
          logsList.innerHTML = "";
          data.logs.forEach(log => {
              logsList.innerHTML += `<li>${log}</li>`;
          });

          // Create charts
           // Create charts dynamically
           createAlertsChart(data.chart.labels, data.chart.values);
           createSeverityChart(data.severityLevels);
      })
      .catch(error => console.error("Error fetching data:", error));
});
// Function to create Alerts Chart
function createAlertsChart(labels, values) {
    const ctxAlerts = document.getElementById('alertsChart').getContext('2d');
    new Chart(ctxAlerts, {
        type: 'line',
        data: {
            labels: labels, // Dynamically populated
            datasets: [{
                label: 'Alerts Over Time',
                data: values, // Dynamically populated
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Months',
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Alerts',
                    },
                    beginAtZero: true,
                },
            },
        }
    });
}

// Function to create Severity Chart
function createSeverityChart(severityLevels) {
    const ctxSeverity = document.getElementById('severityChart').getContext('2d');
    new Chart(ctxSeverity, {
        type: 'bar',
        data: {
            labels: ['High', 'Medium', 'Low'], // Severity Levels
            datasets: [{
                label: 'Severity Levels',
                data: severityLevels, // Dynamically populated
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Severity Levels',
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: 'Count',
                    },
                    beginAtZero: true,
                },
            },
        }
    });
}