document.addEventListener('DOMContentLoaded', function () { //wait until the DOM (HTML document) is fully loaded
    // Form validation
    const form = document.querySelector('form'); //select the first <form> element in the document
    if (form) { //if the form exists
        form.addEventListener('submit', function (e) { //
            const email = document.querySelector('input[type="email"]');
            const linkedin = document.querySelector('input[name="linkedin_profile"]');
            if (email && !validateEmail(email.value)) { //if email exists and is not valid
                e.preventDefault(); //prevent form submission behavior
                alert('Please enter a valid email address'); //show alert message
            }
            if (linkedin && !validateLinkedIn(linkedin.value)) { //if linkedin url exists and is not valid
                e.preventDefault(); //prevent form submission behaviour
                alert('Please enter a valid LinkedIn URL'); //show alert message
            }
        });
    }
});

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        //regular expression to check if the email format is valid; between two slashes
        //^[^\s@]+ -> matches the beginning of the string with any characters except spaces or @
        //@[^\s@]+ -> ensures there's an @ symbol followed by valid characters
        //\.[^\s@]+$ -> ensures there's a period followed by valid characters at the end of the string
}

function validateLinkedIn(url) {
    return url.includes('linkedin.com/'); //check if the URL contains "linkedin.com/" as a substring
}

document.addEventListener('DOMContentLoaded', function () { //wait for the DOM content to be fully loaded
    //select the button or element ued to toggle dark mode by it ID
    const darkModeToggle = document.getElementById('dark-mode-toggler');
    //define a function toc check if the dark-mode class is currently applied to the body
    const darkModeStatus = () => document.body.classList.contains('dark-mode');

    //check if the dark mode was previously enabled and change the text of the button accordingly
    if(localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeToggle.textContent = "Light Mode";
    }

    //add a click event listener to the dark mode toggle button
    darkModeToggle?.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const enabled = darkModeStatus();
        localStorage.setItem('darkMode', enabled ? 'enabled' : 'disabled');
        //update the toggle button text based on the current dark mode status
        darkModeToggle.textContent = enabled ? "Light Mode" : "Dark Mode";
    });

});