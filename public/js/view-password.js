function viewPassword() {
    let passInput;

    if (document.getElementById('registration_form_plainPassword'))
        passInput = document.getElementById('registration_form_plainPassword');
    else
        passInput = document.getElementById('password');

    const passStatus = document.getElementById('pass-status');
    if (passInput.type === 'password'){
        passInput.type = 'text';
        passStatus.className = 'fas fa-eye eye-pos';
    }
}
function hidePassword() {
    let passInput;

    if (document.getElementById('registration_form_plainPassword'))
        passInput = document.getElementById('registration_form_plainPassword');
    else
        passInput = document.getElementById('password');


    const passStatus = document.getElementById('pass-status');
    if (passInput.type === 'text'){
        passInput.type = 'password';
        passStatus.className = 'fas fa-eye-slash eye-pos';
    }
}

const el = document.getElementById('pass-status');
el.addEventListener('mousedown', viewPassword);
el.addEventListener('mouseup', hidePassword);
