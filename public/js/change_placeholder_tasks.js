function changePlaceholder() {
    const choice = document.getElementById('choice');
    const value = document.getElementById('value');

    if (choice.selectedIndex === 0)
        value.placeholder = 'enter the name of the task to do...';
    else
        value.placeholder = 'enter the name of the product to buy...';
}

const choice = document.getElementById('choice');

choice.addEventListener('change', changePlaceholder);