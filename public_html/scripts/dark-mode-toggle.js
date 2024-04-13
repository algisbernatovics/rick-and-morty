document.addEventListener('DOMContentLoaded', function () {
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    console.log('Dark Theme', prefersDark);

    // Function to update the theme on page load
    function updateThemeOnLoad() {
        console.log('updateThemeOnLoad called');
        if (prefersDark === true) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    updateThemeOnLoad();
});