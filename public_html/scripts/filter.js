import { setupPagination } from './pagination.js';
const searchButton = document.getElementById("search-button");
const filter = document.getElementById("filter");

searchButton.addEventListener("click", function () {
    filter.style.display = (filter.style.display === 'none' || filter.style.display === '') ? 'block' : 'none';
    searchButton.classList.toggle("active");
});

document.getElementById('filterForm').addEventListener('input', function () {
    var form = document.getElementById('filterForm');
    var formData = new FormData(form);

    var pageName = document.getElementById('filterForm').getAttribute('data-page-name');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/' + pageName + '/filter', true);
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            var contentIsNotFound = xhr.responseText.trim() === 'Not found!';

            if (contentIsNotFound) {
            } else {
                var singleContentElement = document.getElementById('singleContent');
                if (singleContentElement) {
                    singleContentElement.style.display = contentIsNotFound ? 'block' : 'none';
                } else {
                    console.log('Element with ID "singleContent" not found');
                }
                document.getElementById('filterContent').innerHTML = xhr.responseText;

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
});

document.addEventListener("DOMContentLoaded", function () {
    setupPagination();
});

document.addEventListener('ajaxSuccess', function () {
    setupPagination();
});
