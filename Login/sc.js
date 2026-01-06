const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('showPassword');

eyeIcon.addEventListener('click', function() {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = "oeil.png"; // Changer l’image si tu as une version "œil ouvert"
    } else {
        passwordInput.type = 'password';
        eyeIcon.src = "oeil (2).png"; // Revenir à l’image par défaut
    }
});