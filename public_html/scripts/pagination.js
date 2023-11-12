// pagination.js

function setupPagination() {
    const paginationContainer = document.querySelector('.pagination');

    if (paginationContainer) {
        paginationContainer.addEventListener('click', function (event) {
            event.preventDefault();

            const paginationLink = event.target.closest('a');

            if (paginationLink) {
                const pageUrl = paginationLink.getAttribute('href');

                fetch(pageUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        const contentArea = document.getElementById('filterContent');
                        if (contentArea) {
                            contentArea.innerHTML = data;
                        }

                        setupPagination();
                    })
                    .catch(error => {
                        console.error('Error fetching page:', error);
                    });
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // Initial setup
    setupPagination();
});
