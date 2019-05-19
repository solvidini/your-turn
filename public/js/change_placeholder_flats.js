function changePlaceholder() {
    const choice = document.getElementById('choice');
    const value = document.getElementById('value');

    if (choice.selectedIndex === 0)
        value.placeholder = 'Enter flat identifier...';
    else
        value.placeholder = 'Enter flat name...';
}

const choice = document.getElementById('choice');

choice.addEventListener('change', changePlaceholder);