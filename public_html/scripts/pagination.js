export function setupPagination() {
    const paginationContainer = document.querySelector('.pagination');

    if (paginationContainer) {
        paginationContainer.addEventListener('click', function (event) {
            event.preventDefault();
            console.log('Pagination click event triggered!');
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
                            console.log('Updating content area with fetched data:', data);
                            contentArea.innerHTML = data;

                            // Scroll to the top of the page
                            window.scrollTo({ top: 0, behavior: 'smooth' });

                            setupPagination();
                        } else {
                            console.error('Element with ID "filterContent" not found');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching page:', error);
                    });
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
    console.log('DOMContentLoaded event triggered!');
    // Initial setup
    setupPagination();

    document.addEventListener('ajaxSuccess', function () {
        console.log('ajaxSuccess event triggered!');
        setupPagination();
    });
});
