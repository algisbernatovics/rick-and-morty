const searchButton = document.getElementById("search-button");
const filter = document.getElementById("filter");

searchButton.addEventListener("click", function () {
    if (filter.style.display === "none" || filter.style.display === "") {
        filter.style.display = "block";
    } else {
        filter.style.display = "none";
    }
});
