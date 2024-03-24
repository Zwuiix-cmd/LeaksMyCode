document.addEventListener('DOMContentLoaded', function () {
    function createBox(title, options) {
        const box = document.createElement('div');
        box.classList.add('box');

        const titleElement = document.createElement('p');
        titleElement.textContent = title;
        box.appendChild(titleElement);

        const boxContent = document.createElement('div');
        boxContent.classList.add('box-content', 'scrollable');
        box.appendChild(boxContent);

        options.forEach(option => {
            const label = document.createElement('label');

            if (option.type === 'checkbox') {
                label.classList.add('toggle');

                const toggleLabel = document.createElement('span');
                toggleLabel.classList.add('toggle-label');
                toggleLabel.textContent = option.label;
                label.appendChild(toggleLabel);

                const toggleCheckbox = document.createElement('input');
                toggleCheckbox.classList.add('toggle-checkbox');
                toggleCheckbox.setAttribute('type', 'checkbox');
                toggleCheckbox.setAttribute('checkboxName', option.name); // Ajout de checkboxName
                toggleCheckbox.checked = option.checked;
                label.appendChild(toggleCheckbox);

                const toggleSwitch = document.createElement('div');
                toggleSwitch.classList.add('toggle-switch');
                label.appendChild(toggleSwitch);
            } else if (option.type === 'range') {
                // ... (remaining code for range input)
            } else if (option.type === 'text') {
                const text = document.createElement('p');
                text.textContent = option.label + ': ' + option.value;
                boxContent.appendChild(text);
            }

            boxContent.appendChild(label);
        });

        return box;
    }

    function fetchBoxData() {
        return axios.get("/users")
            .then(response => response.data)
            .catch(error => {
                console.error('Erreur lors de la récupération des données :', error);
                return [];
            });
    }

    function sendData(data) {
        axios.post("/update-admin", data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                console.log('Données envoyées avec succès :', response);
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi des données :', error);
            });
    }

    const boxContainer = document.getElementById('boxContainer');

    fetchBoxData().then(boxData => {
        boxData.forEach(data => {
            const box = createBox(data.title, data.options);
            boxContainer.appendChild(box);
        });
    });

    boxContainer.addEventListener('change', function(event) {
        const checkbox = event.target;
        if (checkbox.type === 'checkbox') {
            const accountName = checkbox.closest('.box').querySelector('p').textContent;
            const checkboxValue = checkbox.checked;
            const checkboxName = checkbox.getAttribute('checkboxName');

            const data = {
                name: accountName,
                type: checkboxName,
                value: checkboxValue
            };

            sendData(data);
        }
    });

    const mainForm = document.getElementById('mainForm');

    mainForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(mainForm);
        sendData(formData);
    });

    mainForm.addEventListener('input', function () {
        const rangeInputs = document.querySelectorAll('input[type="range"]');
        rangeInputs.forEach(rangeInput => {
            const outputElement = rangeInput.nextElementSibling;
            if (outputElement && outputElement.tagName === 'OUTPUT') {
                outputElement.textContent = rangeInput.value + (rangeInput.dataset.unit ? ' ' + rangeInput.dataset.unit : '');
            }
        });
    });
});
