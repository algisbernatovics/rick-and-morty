
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
            if (xhr.responseText === 'redirect') {
                window.location.href = '/Locations';
            } else {
                var contentIsNotFound = xhr.responseText.trim() === 'Not found!';

                if (contentIsNotFound) {
                } else {

                    var singleContentElement = document.getElementById('singleContent');
                    if (singleContentElement) {
                        singleContentElement.style.display = contentIsNotFound ? 'block' : 'none';
                    } else {
                        console.error('Element with ID "singleContent" not found');
                    }
                    document.getElementById('filterContent').innerHTML = xhr.responseText;
                }
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
