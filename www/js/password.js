// js/password.js
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');
    const lengthCriteria = document.getElementById('length-criteria');
    const uppercaseCriteria = document.getElementById('uppercase-criteria');
    const specialCharCriteria = document.getElementById('special-char-criteria');

    // Critères
    const lengthOk = password.length >= 8;
    const uppercaseOk = /[A-Z]/.test(password);
    const specialCharOk = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    // Mettre à jour les messages d'erreur pour chaque critère
    if (lengthOk) {
        lengthCriteria.className = 'text-green-500';
        lengthCriteria.textContent = '✔ Au moins 8 caractères';
    } else {
        lengthCriteria.className = 'text-red-500';
        lengthCriteria.textContent = '✘ Veuillez utiliser au moins 8 caractères';
    }

    if (uppercaseOk) {
        uppercaseCriteria.className = 'text-green-500';
        uppercaseCriteria.textContent = '✔ Au moins 1 majuscule';
    } else {
        uppercaseCriteria.className = 'text-red-500';
        uppercaseCriteria.textContent = '✘ Veuillez inclure au moins 1 majuscule';
    }

    if (specialCharOk) {
        specialCharCriteria.className = 'text-green-500';
        specialCharCriteria.textContent = '✔ Au moins 1 caractère spécial';
    } else {
        specialCharCriteria.className = 'text-red-500';
        specialCharCriteria.textContent = '✘ Veuillez inclure au moins 1 caractère spécial';
    }

    // Compter le nombre de critères remplis
    let criteriaMet = 0;
    if (lengthOk) criteriaMet++;
    if (uppercaseOk) criteriaMet++;
    if (specialCharOk) criteriaMet++;

    // Mettre à jour la barre de progression et le texte
    if (criteriaMet === 0 || criteriaMet === 1) {
        // Faible (rouge)
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500 w-1/3';
        strengthText.textContent = 'Faible';
        strengthText.className = 'text-sm text-red-500 mt-1';
    } else if (criteriaMet === 2) {
        // Moyen (orange)
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500 w-2/3';
        strengthText.textContent = 'Moyen';
        strengthText.className = 'text-sm text-orange-500 mt-1';
    } else {
        // Fort (vert)
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500 w-full';
        strengthText.textContent = 'Fort';
        strengthText.className = 'text-sm text-green-500 mt-1';
    }
}