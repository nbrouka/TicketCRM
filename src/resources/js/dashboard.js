document.addEventListener('DOMContentLoaded', function () {
    // Load initial statistics
    loadDashboardStats();

    // Load daily statistics chart (from last month)
    loadDailyStatistics();

    // Load ticket status distribution chart
    loadStatusDistributionChart();
});

function loadDashboardStats() {
    // Fetch basic statistics from API
    fetch('/tickets/statistics', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('tickets-today').textContent = data.day;
            document.getElementById('tickets-week').textContent = data.week;
            document.getElementById('tickets-month').textContent = data.month;
            document.getElementById('tickets-total').textContent = data.total;
        })
        .catch(error => {
            console.error('Error fetching ticket statistics:', error);
            // In case of error, hide the loading text or show an error message
            document.getElementById('tickets-today').textContent = 'N/A';
            document.getElementById('tickets-week').textContent = 'N/A';
            document.getElementById('tickets-month').textContent = 'N/A';
            document.getElementById('tickets-total').textContent = 'N/A';
        });
}

function loadDailyStatistics(month = 'last_month') {
    // Fetch daily statistics from specified month grouped by status
    fetch(`/tickets/statistics-by-day-data?month=${month}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            renderMonthlyChart(data);
        })
        .catch(error => {
            console.error('Error loading daily statistics:', error);
        });
}

// Add event listener for month selector
const monthSelector = document.getElementById('monthSelector');
if (monthSelector) {
    monthSelector.addEventListener('change', function () {
        loadDailyStatistics(this.value);
    });
}

// Helper function to get CSRF token
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function renderMonthlyChart(data) {
    const ctx = document.getElementById('monthlyDashboardChart').getContext('2d');

    // Prepare data for the chart
    const labels = data.map(item => `${item.day}/${item.month}/${item.year}`);
    const newTickets = data.map(item => item.status_counts['new'] || 0);
    const inProgressTickets = data.map(item => item.status_counts['in_progress'] || 0);
    const doneTickets = data.map(item => item.status_counts['done'] || 0);

    // Destroy existing chart instance if it exists
    if (window.monthlyChart) {
        window.monthlyChart.destroy();
    }

    window.monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'New Tickets',
                    backgroundColor: '#007bff',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    data: newTickets,
                },
                {
                    label: 'In Progress Tickets',
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    borderWidth: 1,
                    data: inProgressTickets,
                },
                {
                    label: 'Done Tickets',
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    borderWidth: 1,
                    data: doneTickets,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: false  // Changed from 'true' to 'false' to show separate bars
                },
                x: {
                    grid: {
                        display: false
                    },
                    stacked: false  // Changed from 'true' to 'false' to show separate bars
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Daily Ticket Statistics by Status'
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            }
        }
    });
}

function loadStatusDistributionChart() {
    // Fetch ticket status distribution from API
    fetch('/tickets/status-distribution', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            renderStatusDistributionChart(data);
        })
        .catch(error => {
            console.error('Error loading status distribution chart:', error);
        });
}

function renderStatusDistributionChart(data) {
    const ctx = document.getElementById('statusDistributionChart').getContext('2d');

    // Prepare data for the chart
    const labels = Object.keys(data).map(status => {
        // Convert status from snake_case to Title Case
        return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    });
    const counts = Object.values(data);

    // Define colors for different statuses
    const backgroundColors = [
        '#007bff', // New - blue
        '#ffc107', // In Progress - yellow
        '#28a745'  // Done - green
    ];

    // Destroy existing chart instance if it exists
    if (window.statusDistributionChart && typeof window.statusDistributionChart.destroy === 'function') {
        window.statusDistributionChart.destroy();
    }

    window.statusDistributionChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ticket Count',
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.2', '1')),
                    borderWidth: 1,
                    data: counts,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Ticket Status Distribution'
                }
            }
        }
    });
}
