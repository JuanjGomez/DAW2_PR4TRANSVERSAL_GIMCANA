document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="password_confirmation"]');

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

    function validateName() {
        const name = nameInput.value.trim();
        if (name === '') {
            showError(nameInput, 'El nombre es requerido');
            return false;
        } else if (/\d/.test(name)) {
            showError(nameInput, 'El nombre no debe contener números');
            return false;
        } else {
            clearError(nameInput);
            return true;
        }
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
        } else if (password.length < 8) {
            showError(passwordInput, 'La contraseña debe tener al menos 8 caracteres');
            return false;
        } else if (!/[A-Z]/.test(password)) {
            showError(passwordInput, 'La contraseña debe contener al menos una mayúscula');
            return false;
        } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            showError(passwordInput, 'La contraseña debe contener al menos un carácter especial');
            return false;
        } else {
            clearError(passwordInput);
            return true;
        }
    }

    function validateConfirmPassword() {
        const confirmPassword = confirmPasswordInput.value.trim();
        const password = passwordInput.value.trim();
        if (confirmPassword === '') {
            showError(confirmPasswordInput, 'Confirma tu contraseña');
            return false;
        } else if (confirmPassword !== password) {
            showError(confirmPasswordInput, 'Las contraseñas no coinciden');
            return false;
        } else {
            clearError(confirmPasswordInput);
            return true;
        }
    }

    nameInput.addEventListener('blur', validateName);
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);
    confirmPasswordInput.addEventListener('blur', validateConfirmPassword);

    form.addEventListener('submit', function(e) {
        if (!validateName() || !validateEmail() || !validatePassword() || !validateConfirmPassword()) {
            e.preventDefault();
        }
    });
}); 