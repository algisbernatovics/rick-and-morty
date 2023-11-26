export async function setupPagination() {
    const paginationContainer = document.querySelector('.pagination');

    if (!paginationContainer) {
        return;
    }

    paginationContainer.addEventListener('click', async function (event) {
        event.preventDefault();
        console.log('Pagination click event triggered!');

        const paginationLink = event.target.closest('a');

        if (!paginationLink) {
            return;
        }

        try {
            const pageUrl = paginationLink.getAttribute('href');
            const response = await fetch(pageUrl);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.text();
            const contentArea = document.getElementById('filterContent');

            if (contentArea) {
                console.log('Updating content area with fetched data:', data);

                // Create a div element to hold the content
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;

                // Wait for all images to load
                const images = tempDiv.querySelectorAll('img');
                const imageLoadPromises = Array.from(images).map(img => {
                    return new Promise((resolve, reject) => {
                        img.onload = resolve;
                        img.onerror = reject;
                    });
                });

                await Promise.all(imageLoadPromises);

                // Clear content area and append the loaded content
                contentArea.innerHTML = '';
                contentArea.appendChild(tempDiv);

                // Pause for 0.1 second before scrolling
                await new Promise(resolve => setTimeout(resolve, 10));

                // Scroll to the top of the page
                window.scrollTo({top: 0, behavior: 'smooth'});

                // Setup pagination after scrolling
                setupPagination();
            } else {
                console.error('Element with ID "filterContent" not found');
            }
        } catch (error) {
            console.error('Error fetching page:', error);
        }
    });
}

document.addEventListener("DOMContentLoaded", async function () {
    console.log('DOMContentLoaded event triggered in pagination.js!');
    await window.scrollTo({top: 0, behavior: 'smooth'});
    await setupPagination();
});
