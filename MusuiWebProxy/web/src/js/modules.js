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
                toggleCheckbox.setAttribute('name', option.name);
                label.appendChild(toggleCheckbox);

                const toggleSwitch = document.createElement('div');
                toggleSwitch.classList.add('toggle-switch');
                label.appendChild(toggleSwitch);
            } else if (option.type === 'range') {
                const rangeLabel = document.createElement('label');
                rangeLabel.setAttribute('for', option.name);
                rangeLabel.textContent = option.label;
                label.appendChild(rangeLabel);

                const rangeInput = document.createElement('input');
                rangeInput.classList.add('slider');
                rangeInput.setAttribute('type', 'range');
                rangeInput.setAttribute('name', option.name);
                rangeInput.setAttribute('id', option.name);
                rangeInput.setAttribute('min', option.min);
                rangeInput.setAttribute('max', option.max);
                rangeInput.setAttribute('step', option.step);
                rangeInput.setAttribute('value', option.value);
                label.appendChild(rangeInput);

                const output = document.createElement('output');
                output.setAttribute('name', `${option.name}_result`);
                output.textContent = option.value;
                label.appendChild(output);

                if (option.unit) {
                    const unitSpan = document.createElement('span');
                    unitSpan.classList.add('unit'); // Ajoutez la classe CSS pour l'unité
                    unitSpan.textContent = ' ' + option.unit;
                    label.appendChild(unitSpan);
                }
            } else if (option.type === 'text') {
                const textInput = document.createElement('input');
                textInput.classList.add('input');
                textInput.setAttribute('type', 'text');
                textInput.setAttribute('name', option.name);
                textInput.setAttribute('placeholder', option.placeholder);
                label.appendChild(textInput);
            }

            boxContent.appendChild(label);
        });

        return box;
    }

    function fetchBoxData() {
        return axios.get("/modules")
            .then(response => response.data)
            .catch(error => {
                console.error('Erreur lors de la récupération des données :', error);
                return [];
            });
    }

    const boxContainer = document.getElementById('boxContainer');

    fetchBoxData().then(boxData => {
        boxData.forEach(data => {
            const box = createBox(data.title, data.options);
            boxContainer.appendChild(box);
        });
    });

    const mainForm = document.getElementById('mainForm');

    mainForm.addEventListener('input', function () {
        const rangeInputs = document.querySelectorAll('input[type="range"]');
        rangeInputs.forEach(rangeInput => {
            const outputElement = rangeInput.nextElementSibling;
            if (outputElement && outputElement.tagName === 'OUTPUT') {
                outputElement.textContent = rangeInput.value;
            }
        });
    });
});
