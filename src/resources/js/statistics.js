document.addEventListener('DOMContentLoaded', function () {
    fetchStatistics();

    // Add event listener for month selector
    const monthSelector = document.getElementById('monthSelectorStats');
    if (monthSelector) {
        monthSelector.addEventListener('change', function () {
            fetchStatistics(this.value);
        });
    }
});

async function fetchStatistics(month = 'last_month') {
    try {
        const response = await fetch(`/tickets/statistics-by-day-data?month=${month}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        renderChart(data);
        renderTable(data);
    } catch (error) {
        console.error('Error fetching statistics:', error);
        alert('Failed to load statistics');
    }
}

function renderChart(data) {
    const ctx = document.getElementById('monthlyStatisticsChart').getContext('2d');

    // Prepare data for the chart
    const labels = data.map(item => `${item.day}/${item.month}/${item.year}`);
    const newTickets = data.map(item => item.status_counts['new'] || 0);
    const inProgressTickets = data.map(item => item.status_counts['in_progress'] || 0);
    const doneTickets = data.map(item => item.status_counts['done'] || 0);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'New Tickets',
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                data: newTickets,
                tension: 0.1
            },
            {
                label: 'In Progress Tickets',
                backgroundColor: '#ffc107',
                borderColor: '#ffc107',
                data: inProgressTickets,
                tension: 0.1
            },
            {
                label: 'Done Tickets',
                backgroundColor: '#28a745',
                borderColor: '#28a745',
                data: doneTickets,
                tension: 0.1
            }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: false,
                },
                y: {
                    stacked: false,
                    beginAtZero: true
                }
            }
        }
    });
}

function renderTable(data) {
    const tbody = document.querySelector('#statisticsTable tbody');
    tbody.innerHTML = '';

    data.forEach(item => {
        const row = document.createElement('tr');

        const dayCell = document.createElement('td');
        dayCell.textContent = item.day;

        const monthCell = document.createElement('td');
        monthCell.textContent = item.month;

        const yearCell = document.createElement('td');
        yearCell.textContent = item.year;

        const newCell = document.createElement('td');
        newCell.textContent = item.status_counts['new'] || 0;

        const inProgressCell = document.createElement('td');
        inProgressCell.textContent = item.status_counts['in_progress'] || 0;

        const doneCell = document.createElement('td');
        doneCell.textContent = item.status_counts['done'] || 0;

        row.appendChild(dayCell);
        row.appendChild(monthCell);
        row.appendChild(yearCell);
        row.appendChild(newCell);
        row.appendChild(inProgressCell);
        row.appendChild(doneCell);

        tbody.appendChild(row);
    });
}
