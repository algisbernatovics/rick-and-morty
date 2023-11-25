document.addEventListener('DOMContentLoaded', function () {

    console.log('Local Storage Theme:', localStorage.theme);

    const switchInput = document.getElementById('darkLightSwitch');

    // Function to update the theme on page load
    function updateThemeOnLoad() {
        const currentTheme = localStorage.theme;
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
            switchInput.checked = true;
        } else {
            document.documentElement.classList.remove('dark');
            switchInput.checked = false;
        }
    }

    // Function to toggle dark mode
    window.toggleDarkMode = function () {
        if (localStorage.theme === 'dark') {
            localStorage.theme = 'light';
        } else {
            localStorage.theme = 'dark';
        }

        document.documentElement.classList.toggle('dark');
        switchInput.checked = document.documentElement.classList.contains('dark');
        console.log('Local Storage Theme set to:', localStorage.theme);
    };

    // Function to toggle the switch
    function toggleSwitch() {
        window.toggleDarkMode(); // Toggle the theme when the switch is clicked
    }

    // Attach the toggleSwitch function to the switch input
    switchInput.addEventListener('click', toggleSwitch);

    // Set the initial theme on page load
    updateThemeOnLoad();
});
