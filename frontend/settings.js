document.addEventListener('DOMContentLoaded', () => {
    fetchRules();
    document.getElementById('settings-form').addEventListener('submit', addRule);
});

function fetchRules() {
    fetch('../php/fetch_rules.php')
        .then(response =>{return response.json();})
        .then(rules => {
            console.log(rules);
            const ruleList = document.getElementById('rule-list');
            rules.forEach(rule => {
                console.log(rule);
                const ruleItem = document.createElement('div');
                ruleItem.classList.add('rule-item');
                ruleItem.textContent = rule;
                ruleList.appendChild(ruleItem);
            });
        })
        .catch(error => console.error('Error fetching rules:', error));
}

function addRule(event) {
    event.preventDefault();
    const ruleInput = document.getElementById('rule');
    const rule = ruleInput.value.trim();

    if (rule) {
        fetch('../php/add_rule.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rule })
        })
        .then(response =>
        {
           return response.json();})
        .then(data => {
            if (data.success) {
                const ruleItem = document.createElement('div');
                ruleItem.classList.add('rule-item');
                ruleItem.textContent = rule;
                document.getElementById('rule-list').appendChild(ruleItem);
                ruleInput.value = '';
            } else {
                alert('Error adding rule: ' + data.message);
            }
        })
        .catch(error => console.error('Error adding rule:', error));
    }
}