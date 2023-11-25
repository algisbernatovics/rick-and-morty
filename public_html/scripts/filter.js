import { setupPagination } from './pagination.js';

const searchButton = document.getElementById("search-button");
const filter = document.getElementById("filter");

searchButton.addEventListener("click", function () {
    filter.style.display = (filter.style.display === 'none' || filter.style.display === '') ? 'block' : 'none';
    searchButton.classList.toggle("active");
});

let typingTimer; // Timer identifier
const doneTypingInterval = 1000; // 1 second

function fetchData() {
    var form = document.getElementById('filterForm');
    var formData = new FormData(form);

    var pageName = document.getElementById('filterForm').getAttribute('data-page-name');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/' + pageName + '/filter', true);
    xhr.onload = async function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            var contentIsNotFound = xhr.responseText.trim() === 'Not found!';

            if (contentIsNotFound) {
                // Handle not found case
            } else {
                var singleContentElement = document.getElementById('singleContent');
                if (singleContentElement) {
                    singleContentElement.style.display = contentIsNotFound ? 'block' : 'none';
                } else {
                    console.log('Element with ID "singleContent" not found');
                }

                // Update content area with fetched data
                const contentArea = document.getElementById('filterContent');
                contentArea.innerHTML = xhr.responseText;

                // Wait for all images to load
                const images = contentArea.querySelectorAll('img');
                const imageLoadPromises = Array.from(images).map(img => {
                    return new Promise((resolve, reject) => {
                        img.onload = resolve;
                        img.onerror = reject;
                    });
                });

                await Promise.all(imageLoadPromises);

                // Scroll to the top of the page
                window.scrollTo({top: 0, behavior: 'smooth'});

                // Setup pagination after scrolling
                setupPagination();
            }
        } else {
            console.error('Request failed with status ' + xhr.status);
        }
    };
    xhr.onerror = function () {
        console.error('Network error occurred');
    };
    xhr.send(formData);
}

// Event listener for filter input with debounce
document.getElementById('filterForm').addEventListener('input', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(fetchData, doneTypingInterval);
});

document.addEventListener("DOMContentLoaded", async function () {
    console.log('DOMContentLoaded event triggered in filter.js!');
    await window.scrollTo({top: 0, behavior: 'smooth'});
    await setupPagination();
});