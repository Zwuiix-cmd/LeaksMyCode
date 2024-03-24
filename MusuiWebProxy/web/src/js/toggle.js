const toggleCheckbox = document.getElementById('toggle-checkbox');
const toggleSwitch = document.querySelector('.toggle-switch');

toggleCheckbox.addEventListener('change', function() {
    if (toggleCheckbox.checked) {
        toggleSwitch.style.backgroundColor = '#4CAF50';
        toggleSwitch.style.transform = 'translateX(20px)';
    } else {
        toggleSwitch.style.backgroundColor = '#ccc';
        toggleSwitch.style.transform = 'translateX(0)';
    }
});