const searchButton = document.getElementById("search-button");
const filter = document.getElementById("filter");

searchButton.addEventListener("click", function () {

    filter.style.display = (filter.style.display === 'none' || filter.style.display === '') ? 'block' : 'none';

    searchButton.classList.toggle("active");
});