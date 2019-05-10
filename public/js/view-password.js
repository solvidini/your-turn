function viewPassword() {
    const passInput = document.getElementById('registration_form_plainPassword');
    const passStatus = document.getElementById('pass-status');
    if (passInput.type == 'password'){
        passInput.type = 'text';
        passStatus.className = 'fas fa-eye-slash eye-pos';
    }
}
function hidePassword() {
    const passInput = document.getElementById('registration_form_plainPassword');
    const passStatus = document.getElementById('pass-status');
    if (passInput.type == 'text'){
        passInput.type = 'password';
        passStatus.className = 'fas fa-eye eye-pos';
    }
}

const el = document.getElementById('pass-status');
el.addEventListener('mousedown', viewPassword);
el.addEventListener('mouseup', hidePassword);
