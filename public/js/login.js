document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');

    function showError(input, message) {
        const formControl = input.parentElement;
        const error = formControl.querySelector('.error-message') || document.createElement('span');
        error.className = 'error-message';
        error.textContent = message;
        
        if (!formControl.querySelector('.error-message')) {
            formControl.appendChild(error);
        }
        
        input.classList.add('error');
    }

    function clearError(input) {
        const formControl = input.parentElement;
        const error = formControl.querySelector('.error-message');
        if (error) {
            error.remove();
        }
        input.classList.remove('error');
    }

    function validateEmail() {
        const email = emailInput.value.trim();
        if (email === '') {
            showError(emailInput, 'El email es requerido');
            return false;
        } else if (!/\S+@\S+\.\S+/.test(email)) {
            showError(emailInput, 'Por favor ingresa un email válido');
            return false;
        } else {
            clearError(emailInput);
            return true;
        }
    }

    function validatePassword() {
        const password = passwordInput.value.trim();
        if (password === '') {
            showError(passwordInput, 'La contraseña es requerida');
            return false;
        } else if (password.length < 6) {
            showError(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
            return false;
        } else {
            clearError(passwordInput);
            return true;
        }
    }

    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);

    form.addEventListener('submit', function(e) {
        if (!validateEmail() || !validatePassword()) {
            e.preventDefault();
        }
    });
}); 