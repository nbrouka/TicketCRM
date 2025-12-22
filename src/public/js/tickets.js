// Ticket pagination functionality
document.addEventListener('DOMContentLoaded', function () {
    // Handle cursor-based pagination with AJAX
    const paginationLinks = document.querySelectorAll('.pagination a');

    paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.getAttribute('href');
            if (!url) return;

            // Show loading state
            const paginationContainer = this.closest('.pagination');
            if (!paginationContainer) return;

            const loadingElement = document.createElement('li');
            loadingElement.className = 'page-item disabled';
            loadingElement.innerHTML = '<span class="page-link">Loading...</span>';

            // Replace pagination with loading indicator
            const parentUl = paginationContainer.querySelector('ul');
            if (parentUl) {
                parentUl.innerHTML = '';
                parentUl.appendChild(loadingElement);
            }

            // Fetch new page via AJAX
            if (url) {
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                    .then(response => response.text())
                    .then(html => {
                        // Parse the HTML response to extract the table body and new pagination
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Get the new table body and pagination
                        const newTableBody = doc.querySelector('#tickets-table tbody');
                        const newPagination = doc.querySelector('.pagination');

                        const currentTableBody = document.querySelector(
                            '#tickets-table tbody');
                        const currentPagination = document.querySelector('.pagination');

                        if (newTableBody && currentTableBody) {
                            currentTableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;

                            // Reattach event listeners to new pagination links
                            const newPaginationLinks = currentPagination.querySelectorAll(
                                'a');
                            if (newPaginationLinks && newPaginationLinks.length > 0) {
                                newPaginationLinks.forEach(newLink => {
                                    newLink.addEventListener('click',
                                        handlePaginationClick);
                                });
                            }
                        }

                        // Update URL in browser without page refresh
                        history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Error loading pagination:', error);
                        // Reload the page on error
                        window.location.href = url;
                    });
            }
        });
    });

    // Define pagination click handler function for re-use
    function handlePaginationClick(e) {
        e.preventDefault();

        const url = this.getAttribute('href');
        if (!url) return;

        // Show loading state
        const paginationContainer = this.closest('.pagination');
        if (!paginationContainer) return;

        const loadingElement = document.createElement('li');
        loadingElement.className = 'page-item disabled';
        loadingElement.innerHTML = '<span class="page-link">Loading...</span>';

        // Replace pagination with loading indicator
        const parentUl = paginationContainer.querySelector('ul');
        if (parentUl) {
            parentUl.innerHTML = '';
            parentUl.appendChild(loadingElement);
        }

        // Fetch new page via AJAX
        if (url) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
                .then(response => response.text())
                .then(html => {
                    // Parse the HTML response to extract the table body and new pagination
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Get the new table body and pagination
                    const newTableBody = doc.querySelector('#tickets-table tbody');
                    const newPagination = doc.querySelector('.pagination');

                    const currentTableBody = document.querySelector('#tickets-table tbody');
                    const currentPagination = document.querySelector('.pagination');

                    if (newTableBody && currentTableBody) {
                        currentTableBody.innerHTML = newTableBody.innerHTML;
                    }

                    if (newPagination && currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;

                        // Reattach event listeners to new pagination links
                        const newPaginationLinks = currentPagination.querySelectorAll('a');
                        if (newPaginationLinks && newPaginationLinks.length > 0) {
                            newPaginationLinks.forEach(newLink => {
                                newLink.addEventListener('click', handlePaginationClick);
                            });
                        }
                    }

                    // Update URL in browser without page refresh
                    history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error loading pagination:', error);
                    // Reload the page on error
                    window.location.href = url;
                });
        }
    }

    // Handle filter form submission to maintain cursor pagination context
    const filterForm = document.querySelector('.filter-form-row');
    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            // When filters change, we need to reset cursor pagination
            // by removing cursor parameter from the URL
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('cursor'); // Reset cursor pagination when filtering
        });
    }
});
