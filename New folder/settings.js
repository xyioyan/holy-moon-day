document.addEventListener("DOMContentLoaded", function () {
  // Fetch and display rules on page load
  fetchRules();

  // Add rule form submission
  document.getElementById("add-rule-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const ruleData = {
      action: "addRule",
      rule_name: document.getElementById("rule-name").value.trim(),
      condition: document.getElementById("rule-condition").value.trim(),
      severity: document.getElementById("rule-severity").value,
      is_active: document.getElementById("rule-status").value === "Active" ? 1 : 0,
    };
    if (validateRuleData(ruleData)) {
      sendRequest(ruleData, "Rule added successfully!", () => {
        fetchRules();
        document.getElementById("add-rule-form").reset(); // Reset after request completion
      });
    }
  });

  // Notification preferences form submission
  document.getElementById("notifications-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const notificationData = {
      action: "updateNotifications",
      email_notifications: document.getElementById("email-notifications").checked ? 1 : 0,
      sms_notifications: document.getElementById("sms-notifications").checked ? 1 : 0,
    };
    sendRequest(notificationData, "Notification preferences updated!");
  });
});

// Validate rule data before submission
function validateRuleData(ruleData) {
  if (!ruleData.rule_name || !ruleData.condition || !ruleData.severity) {
    alert("All fields are required.");
    return false;
  }
  return true;
}

// Fetch rules from the server
function fetchRules() {
  fetch("../php/settings.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: 'fetchRules' }) // Ensure 'action' is set correctl
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const tableBody = document.querySelector("#existing-rules table tbody");
        tableBody.innerHTML = "";

        data.rules.forEach(rule => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${rule.id}</td>
            <td>${rule.rule_name}</td>
            <td>${rule.condition}</td>
            <td>${rule.severity}</td>
            <td>${rule.is_active ? "Active" : "Inactive"}</td>
            <td>
              <button class="edit-btn" data-id="${rule.id}">Edit</button>
              <button class="delete-btn" data-id="${rule.id}">Delete</button>
            </td>
          `;
          tableBody.appendChild(row);
        });

        // Attach event listeners after adding elements
        attachEventListeners();
      } else {
        alert("Failed to fetch rules.");
      }
    })
    .catch(error => {
      console.error("Error fetching rules:", error);
    });
}

// Function to attach event listeners to dynamically created buttons
function attachEventListeners() {
  document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", function () {
      openEditModal(this.dataset.id);
    });
  });

  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", function () {
      deleteRule(this.dataset.id);
    });
  });
}

// Open the edit modal and populate fields
function openEditModal(ruleId) {
  fetch("../php/settings.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "getRule", rule_id: ruleId }),
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById("edit-rule-id").value = data.rule.id;
        document.getElementById("edit-rule-name").value = data.rule.rule_name;
        document.getElementById("edit-rule-condition").value = data.rule.condition;
        document.getElementById("edit-rule-severity").value = data.rule.severity;
        document.getElementById("edit-rule-status").value = data.rule.is_active ? "Active" : "Inactive";

        // Show the modal (assuming you're using a modal system)
        document.getElementById("edit-rule-modal").style.display = "block";
      } else {
        alert("Failed to load rule data.");
      }
    })
    .catch(error => {
      console.error("Error fetching rule data:", error);
    });
}

// Function to close the edit modal
function closeEditModal() {
  document.getElementById("edit-rule-modal").style.display = "none";
}

// Edit rule function
function editRule() {
  const ruleId = document.getElementById("edit-rule-id").value;
  const ruleName = document.getElementById("edit-rule-name").value.trim();
  const condition = document.getElementById("edit-rule-condition").value.trim();
  const severity = document.getElementById("edit-rule-severity").value;
  const isActive = document.getElementById("edit-rule-status").value === "Active" ? 1 : 0;

  if (!ruleId || !ruleName || !condition || !severity) {
    alert("All fields are required.");
    return;
  }

  const ruleData = {
    action: "editRule",
    rule_id: ruleId,
    rule_name: ruleName,
    condition: condition,
    severity: severity,
    is_active: isActive,
  };
  
  sendRequest(ruleData, "Rule edited successfully!", fetchRules, );
  closeEditModal()
}

// Delete rule function
function deleteRule(ruleId) {
  if (confirm("Are you sure you want to delete this rule?")) {
    sendRequest(
      { action: "deleteRule", rule_id: ruleId },
      "Rule deleted successfully!",
      fetchRules
    );
  }
}

// Send request to the server
function sendRequest(data, successMessage, callback = null) {
  fetch("../php/settings.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then(response => response.json())
    .then(data => {
      console.log("Server Response:", data); // Debugging line
      if (data.success) {
        alert(successMessage);
        if (callback) callback();
      } else {
        alert(data.message || "An error occurred.");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      alert("An error occurred while processing the request.");
    });
}
